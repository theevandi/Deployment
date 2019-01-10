<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Payment Payment
 * @ingroup     UnaModules
 * 
 * @{
 */


class BxPaymentGridCart extends BxBaseModPaymentGridCarts
{
    public function __construct ($aOptions, $oTemplate = false)
    {
    	$this->MODULE = 'bx_payment';

        parent::__construct ($aOptions, $oTemplate);

        $this->_bSelectAll = true;
    }

    public function performActionCheckout()
    {
    	$aParams = array(
			'seller_id' => bx_process_input(bx_get('seller_id'), BX_DATA_INT), 
			'provider' => bx_process_input(bx_get('provider')), 
			'items' => bx_process_input(bx_get('ids'))    		
    	);
    	if(empty($aParams['seller_id']) || empty($aParams['provider']))
    		return echoJson(array());

        if(empty($aParams['items']) || !is_array($aParams['items'])) 
        	return echoJson(array('msg' => _t('_bx_payment_err_nothing_selected')));

		$oProvider = $this->_oModule->getObjectProvider($aParams['provider'], $aParams['seller_id']);
		if($oProvider !== false && method_exists($oProvider, 'getCheckoutParamsSingle'))
			$aParams = $oProvider->getCheckoutParamsSingle($aParams, $this);

		$sLink = $this->_oModule->_oConfig->getUrl('URL_CART_CHECKOUT');
		$sLink = $sLink . (strpos($sLink, '?') === false ? '?' : '&') . http_build_query($aParams);

		echoJson(array(
			'eval' => $this->_oModule->_oConfig->getJsObject('cart') . '.onCartCheckout(oData);', 
			'link' => $sLink
		));
    }

    protected function _getCellTitle($mixedValue, $sKey, $aField, $aRow)
    {
    	return parent::_getCellDefault($this->_oModule->_oTemplate->displayLink('link', array(
            'href' => $aRow['url'],
            'title' => bx_html_attribute($aRow['title']),
            'content' => $aRow['title']
    	)), $sKey, $aField, $aRow);
    }

	protected function _getCellPriceSingle($mixedValue, $sKey, $aField, $aRow)
    {
        return parent::_getCellDefault($this->_sCurrencySign . $mixedValue, $sKey, $aField, $aRow);
    }

    protected function _getActionCheckout ($sType, $sKey, $a, $isSmall = false, $isDisabled = false, $aRow = array())
    {
        return '';
    }

    protected function _getActions ($sType, $sActionData = false, $isSmall = false, $isDisabled = false, $isPermanentState = false, $aRow = array())
    {
    	$CNF = &$this->_oModule->_oConfig->CNF;

    	$sActions = '';
    	if($sType == 'bulk' && !empty($this->_aQueryAppend['seller_id'])) {
    		$sActionName = 'checkout';

    		$iClientId = (int)$this->_aQueryAppend['client_id'];
            $iSellerId = (int)$this->_aQueryAppend['seller_id'];

            $oCart = $this->_oModule->getObjectCart();
    		$aProviders = $this->_oModule->_oDb->getVendorInfoProvidersSingle($iSellerId);
    		foreach($aProviders as $aProvider) {
				$sAction = $this->_getActionDefault($sType, $sActionName, array(
					'title'=> _t('_bx_payment_grid_action_title_crt_checkout', _t($CNF['T']['TXT_CART_PROVIDER'] . $aProvider['name'])),
	    			'icon' => '',
					'icon_only' => 0,
	    			'confirm' => 0,
					'attr' => array(
						'bx_grid_action_' . $sType => $sActionName,
						'bx_grid_action_append' => json_encode(array('provider' => $aProvider['name'])),
						'bx_grid_action_confirm' => 0,
						'bx_grid_action_reset_paginate' => 0,
					)
				), $isSmall, $isDisabled, $aRow);

    			$oProvider = $this->_oModule->getObjectProvider($aProvider['name'], $iSellerId);
    			if($oProvider !== false && method_exists($oProvider, 'getButtonSingle')) {
    			    $aParams = array(
    					'sObjNameGrid' => $this->getObject()
    				);

    			    $aCartInfo = $oCart->getInfo(BX_PAYMENT_TYPE_SINGLE, $iClientId, $iSellerId);
    			    if(!empty($aCartInfo['items_price']) && !empty($aCartInfo['items']) && is_array($aCartInfo['items'])) {
    			        $aTitles = array();
    			        foreach ($aCartInfo['items'] as $aItem)
    			            $aTitles[] = $aItem['title'];
    
    			        $aParams = array_merge($aParams, array(
    			            'iAmount' => (int)round(100 * (float)$aCartInfo['items_price']),
    			        	'sItemTitle' => implode(', ', $aTitles)
    			        ));
    			    }

    				$sAction = $oProvider->getButtonSingle($iClientId, $iSellerId, $aParams);
    			}

    			$sActions .= $sAction;
    		}
    	}

    	return $sActions . parent::_getActions ($sType, $sActionData, $isSmall, $isDisabled, $isPermanentState, $aRow);
    }

	protected function _getDataArray($sFilter, $sOrderField, $sOrderDir, $iStart, $iPerPage)
    {
		if(empty($this->_aQueryAppend['client_id']) || empty($this->_aQueryAppend['seller_id']))
			return array();

		$aCart = $this->_oCart->getInfo(BX_PAYMENT_TYPE_SINGLE, $this->_aQueryAppend['client_id'], $this->_aQueryAppend['seller_id']);
		if(empty($aCart) || empty($aCart['items']) || !is_array($aCart['items']))
			return array();

        $this->_aOptions['source'] = array();
		foreach($aCart['items'] as $aCartItem) {
			$aCartItem['descriptor'] = $this->_oModule->_oConfig->descriptorA2S(array($aCart['vendor_id'], $aCartItem['module_id'], $aCartItem['id'], $aCartItem['quantity']));
			$aCartItem['description'] = strip_tags($aCartItem['description']);

			$this->_aOptions['source'][] = $aCartItem;
		}

        return parent::_getDataArray($sFilter, $sOrderField, $sOrderDir, $iStart, $iPerPage);
    }

    protected function _delete($mixedId)
    {
    	list($iVendorId, $iModuleId, $iItemId) = $this->_oModule->_oConfig->descriptorS2A($mixedId);
    	if((int)$iVendorId != (int)$this->_aQueryAppend['seller_id'])
    		return false;

    	return $this->_oCart->serviceDeleteFromCart($iVendorId, $iModuleId, $iItemId);
    }
}

/** @} */
