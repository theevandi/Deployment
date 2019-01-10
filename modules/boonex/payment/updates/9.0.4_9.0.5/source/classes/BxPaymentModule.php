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

define('BX_PAYMENT_ORDERS_TYPE_PENDING', 'pending');
define('BX_PAYMENT_ORDERS_TYPE_PROCESSED', 'processed');
define('BX_PAYMENT_ORDERS_TYPE_SUBSCRIPTION', 'subscription');
define('BX_PAYMENT_ORDERS_TYPE_HISTORY', 'history');

define('BX_PAYMENT_EMPTY_ID', 0);

define('BX_PAYMENT_RESULT_SUCCESS', 0);

/**
 * Payment module by BoonEx
 *
 * This module is needed to work with payment providers and organize the process
 * of some item purchasing. Shopping Cart and Orders Manager are included.
 *
 * Integration notes:
 * To integrate your module with this one, you need:
 * 1. Get 'Add To Cart' button using serviceGetAddToCartLink service.
 * 2. Add info about your module in the 'bx_pmt_modules' table.
 * 3. Realize the following service methods in your Module class.
 *   a. serviceGetItems($iSellerId) - Is used in Orders Administration to get all products of the requested seller(vendor).
 *   b. serviceGetCartItem($iClientId, $iItemId) - Is used in Shopping Cart to get one product by specified id.
 *   c. serviceRegisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) - Register purchased product.
 *   d. serviceUnregisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) - Unregister the product purchased earlier.
 * @see You may see an example of integration in Membership module.
 *
 *
 * Profile's Wall:
 * no spy events
 *
 *
 *
 * Spy:
 * no spy events
 *
 *
 *
 * Memberships/ACL:
 * Doesn't depend on user's membership.
 *
 *
 *
 * Service methods:
 *
 * Is used to get "Add to cart" link for some item(s) in your module.
 * @see BxPmtModule::serviceGetAddToCartLink
 * BxDolService::call('payment', 'get_add_to_cart_link', array($iSellerId, $mixedModuleId, $iItemId, $iItemCount));
 *
 * Check transaction(s) in database which satisty all conditions.
 * @see BxPmtModule::serviceGetOrdersInfo
 * BxDolService::call('payment', 'get_orders_info', array($aConditions), 'Orders');
 *
 * Get total count of items in Shopping Cart.
 * @see BxPmtModule::serviceGetCartItemsCount
 * BxDolService::call('payment', 'get_cart_items_count', array($iUserId, $iOldCount));
 * @note is needed for internal usage(integration with member tool bar).
 *
 * Get Shopping cart content.
 * @see BxPmtModule::serviceGetCartItems
 * BxDolService::call('payment', 'get_cart_items');
 * @note is needed for internal usage(integration with member tool bar).
 *
 *
 * Alerts:
 * no alerts available
 *
 */
class BxPaymentModule extends BxBaseModPaymentModule
{
    protected $_iUserId;

    protected $_aOrderTypes;

    function __construct($aModule)
    {
        parent::__construct($aModule);

        $this->_iUserId = $this->getProfileId();

        $this->_aOrderTypes = array(
        	BX_PAYMENT_ORDERS_TYPE_PENDING, 
        	BX_PAYMENT_ORDERS_TYPE_PROCESSED, 
        	BX_PAYMENT_ORDERS_TYPE_SUBSCRIPTION, 
        	BX_PAYMENT_ORDERS_TYPE_HISTORY
        );
    }

    /**
     * Manage Orders Methods
     */
	public function actionGetClients()
    {
        $sTerm = bx_get('term');

        $aResult = BxDolService::call('system', 'profiles_search', array($sTerm), 'TemplServiceProfiles');

        echoJson($aResult);
    }

    public function actionGetItems($sType, $iModuleId)
    {
    	$iSellerId = $this->getProfileId();
        $aItems = $this->callGetCartItems((int)$iModuleId, array($iSellerId));

		echoJson(array(
			'code' => 0, 
			'data' => $this->_oTemplate->displayItems($sType, $aItems)
		));
    }


    /**
     * Payment Details Methods
     */
    public function serviceIsAcceptingPayments($iVendorId, $sPaymentType = '')
    {
    	$bResult = false;

    	switch($sPaymentType) {
    		case BX_PAYMENT_TYPE_SINGLE:
    			$aProvidersCart = $this->_oDb->getVendorInfoProvidersSingle($iVendorId);
    			$bResult = !empty($aProvidersCart);
    			break;

    		case BX_PAYMENT_TYPE_RECURRING:
    			$aProvidersSubscription = $this->_oDb->getVendorInfoProvidersRecurring($iVendorId);
    			$bResult = !empty($aProvidersSubscription);
    			break;

    		default:
    			$aProviders = $this->_oDb->getVendorInfoProviders($iVendorId);
    			$bResult = !empty($aProviders);
    	}

		return $bResult;
    }

    public function serviceIsPaymentProvider($iVendorId, $sVendorProvider, $sPaymentType = '')
    {
    	$aProvider = $this->serviceGetPaymentProvider($iVendorId, $sVendorProvider, $sPaymentType);
    	return $aProvider !== false;
    }

    public function serviceGetPaymentProvider($iVendorId, $sVendorProvider, $sPaymentType = '')
    {
    	$aProviders = array();
    	switch($sPaymentType) {
    		case BX_PAYMENT_TYPE_SINGLE:
    			$aProviders = $this->_oDb->getVendorInfoProvidersSingle($iVendorId);
    			break;

    		case BX_PAYMENT_TYPE_RECURRING:
    			$aProviders = $this->_oDb->getVendorInfoProvidersRecurring($iVendorId);
    			break;

    		default:
    			$aProviders = $this->_oDb->getVendorInfoProviders($iVendorId);
    	}

    	return !empty($aProviders) && !empty($aProviders[$sVendorProvider]) && is_array(($aProviders[$sVendorProvider])) ? $aProviders[$sVendorProvider] : false;
    }

    public function serviceGetOptionsDefaultCurrencyCode()
    {
        $CNF = &$this->_oConfig->CNF;

        $aCurrencies = BxDolForm::getDataItems($CNF['OBJECT_FORM_PRELISTS_CURRENCIES']);

        $aResult = array();
        foreach($aCurrencies as $sKey => $sValue)
            $aResult[] = array(
                'key' => $sKey,
                'value' => $sValue
            );

        return $aResult;
    }

    public function serviceGetOptionsSiteAdmin()
    {
        $aResult = array(
            array('key' => '', 'value' => _t('_Select_one'))
        );

        $aIds = $this->_oDb->getAdminsIds();
        foreach($aIds as $iId) {
        	$aUser = $this->getProfileInfo($iId);

            $aResult[] = array(
                'key' => $iId,
                'value' => $aUser['name']
            );
        }

        return $aResult;
    }

    /**
     * Cart Processing Methods
     */
    public function actionAddToCart($iSellerId, $iModuleId, $iItemId, $iItemCount)
    {
        $aResult = $this->getObjectCart()->serviceAddToCart($iSellerId, $iModuleId, $iItemId, $iItemCount);
		echoJson($aResult);
    }

    /**
     * Isn't used yet.
     */
    public function actionDeleteFromCart($iSellerId, $iModuleId, $iItemId)
    {
        $aResult = $this->getObjectCart()->serviceDeleteFromCart($iSellerId, $iModuleId, $iItemId);
        echoJson($aResult);
    }

    /**
     * Isn't used yet.
     */
    public function actionEmptyCart($iSellerId)
    {
        $aResult = $this->getObjectCart()->serviceDeleteFromCart($iSellerId);
		echoJson($aResult);
    }

	public function actionSubscribe()
    {
    	$iSellerId = bx_process_input(bx_get('seller_id'), BX_DATA_INT);
    	$sSellerProvider = bx_process_input(bx_get('seller_provider'));
    	$iModuleId = bx_process_input(bx_get('module_id'), BX_DATA_INT);
    	$iItemId = bx_process_input(bx_get('item_id'), BX_DATA_INT);
    	$iItemCount = bx_process_input(bx_get('item_count'), BX_DATA_INT);
    	if(empty($iItemCount))
    		$iItemCount = 1;

        $aResult = $this->getObjectCart()->serviceSubscribe($iSellerId, $sSellerProvider, $iModuleId, $iItemId, $iItemCount);
        $bRedirect = !empty($aResult['redirect']);

        if(!empty($aResult['popup'])) {
            $sContent = '';
            $sContent .= $this->_oTemplate->displayCartJs(BX_PAYMENT_TYPE_RECURRING, $iSellerId);
            $sContent .= !empty($aResult['popup']['html']) ? $aResult['popup']['html'] : $aResult['popup'];

			return $this->_oTemplate->displayPageCodeResponse($sContent, false, true);
        }

		if(!empty($aResult['code']))
			return $this->_oTemplate->displayPageCodeError($aResult['message']);

		if(!empty($aResult['message']) && !$bRedirect)
			return $this->_oTemplate->displayPageCodeResponse($aResult['message']);

        if($bRedirect) {
    		header('Location: ' . $aResult['redirect']);
            exit;
        }
    }

    public function actionSubscribeJson()
    {
    	$iSellerId = bx_process_input(bx_get('seller_id'), BX_DATA_INT);
    	$sSellerProvider = bx_process_input(bx_get('seller_provider'));
    	$iModuleId = bx_process_input(bx_get('module_id'), BX_DATA_INT);
    	$iItemId = bx_process_input(bx_get('item_id'), BX_DATA_INT);
    	$iItemCount = bx_process_input(bx_get('item_count'), BX_DATA_INT);
    	if(empty($iItemCount))
    		$iItemCount = 1;
        $sRedirect = bx_process_input(bx_get('redirect'));

        $aResult = $this->getObjectCart()->serviceSubscribe($iSellerId, $sSellerProvider, $iModuleId, $iItemId, $iItemCount, $sRedirect);
		echoJson($aResult);
    }

    public function actionSubscriptionGetDetails($iId)
    {
        $aResult = $this->_oTemplate->displaySubscriptionGetDetails($iId);
		echoJson($aResult);
    }

    public function actionSubscriptionChangeDetails($iId)
    {
        $aResult = $this->_oTemplate->displaySubscriptionChangeDetails($iId);
		echoJson($aResult);
    }

    public function actionSubscriptionGetBilling($iId)
    {
        $aResult = $this->_oTemplate->displaySubscriptionGetBilling($iId);
		echoJson($aResult);
    }

    public function actionSubscriptionChangeBilling($iId)
    {
        $aResult = $this->_oTemplate->displaySubscriptionChangeBilling($iId);
		echoJson($aResult);
    }

    public function actionSubscriptionCancelation($iId)
    {
        $aResult = array('code' => 1, 'message' => _t('_bx_payment_err_cannot_perform'));

        $aPending = $this->_oDb->getOrderPending(array('type' => 'id', 'id' => $iId));
        if(empty($aPending) && !is_array($aPending))
            return echoJson($aResult);

        $aSubscription = $this->_oDb->getSubscription(array('type' => 'pending_id', 'pending_id' => $iId));
        if(empty($aSubscription) && !is_array($aSubscription))
            return echoJson($aResult);

        $oRecipient = BxDolProfile::getInstance((int)$aPending['seller_id']);
        if(!$oRecipient)
            return echoJson($aResult);

		$aTemplate = BxDolEmailTemplates::getInstance()->parseTemplate($this->_oConfig->getPrefix('general') . 'cancelation_request', array(
			'sibscription_id' => $aSubscription['subscription_id'],
			'sibscription_customer' => $aSubscription['customer_id'],
		    'sibscription_date' => bx_time_js($aSubscription['date'], BX_FORMAT_DATE, true)
		), 0, (int)$aPending['client_id']);

		$sEmail = '';
		$oProvider = $this->getObjectProvider($aPending['provider'], $aPending['seller_id']);
		if($oProvider !== false && $oProvider->isActive())
		    $sEmail = $oProvider->getOption('cancellation_email');

		if(empty($sEmail))
		    $sEmail = $oRecipient->getAccountObject()->getEmail();

		if(!sendMail($sEmail, $aTemplate['Subject'], $aTemplate['Body'], 0, array(), BX_EMAIL_SYSTEM))
		    return echoJson($aResult);

        echoJson(array('code' => 0, 'message' => _t('_bx_payment_msg_cancelation_request_sent')));
    }


    /**
     * Payment Processing Methods
     */
	public function actionInitializeCheckout($sType)
    {
    	if(!$this->isLogged())
            return $this->_oTemplate->displayPageCodeError($this->_sLangsPrefix . 'err_required_login');

		if(bx_get('seller_id') !== false && bx_get('provider') !== false && bx_get('items') !== false) {
			$iSellerId = bx_process_input(bx_get('seller_id'), BX_DATA_INT);
			$sProvider = bx_process_input(bx_get('provider'));
			$aItems = bx_process_input(bx_get('items'));

			$mixedResult = $this->serviceInitializeCheckout(BX_PAYMENT_TYPE_SINGLE, $iSellerId, $sProvider, $aItems);
			if($mixedResult !== true)
	    		return $this->_oTemplate->displayPageCodeError($mixedResult);
		}

        header('Location: ' . $this->_oConfig->getUrl('URL_CART'));
        exit;
    }

	public function serviceInitializeCheckout($sType, $iSellerId, $sProvider, $aItems = array(), $sRedirect = '')
	{
		if(!is_array($aItems))
			$aItems = array($aItems);

		$iSellerId = (int)$iSellerId;
        if($iSellerId == BX_PAYMENT_EMPTY_ID)
            return $this->_sLangsPrefix . 'err_unknown_vendor';

		$oProvider = $this->getObjectProvider($sProvider, $iSellerId);
        if($oProvider === false || !$oProvider->isActive())
        	return $this->_sLangsPrefix . 'err_incorrect_provider';

        $aInfo = $this->getObjectCart()->getInfo($sType, $this->_iUserId, $iSellerId, $aItems);
        if(empty($aInfo) || $aInfo['vendor_id'] == BX_PAYMENT_EMPTY_ID || empty($aInfo['items']))
            return $this->_sLangsPrefix . 'err_empty_order';

		/*
		 * Process FREE (price = 0) items for LOGGED IN members
		 * WITHOUT processing via payment provider.
		 */
		$bProcessedFree = false;
		$sKeyPriceSingle = $this->_oConfig->getKey('KEY_ARRAY_PRICE_SINGLE');
		$sKeyPriceRecurring = $this->_oConfig->getKey('KEY_ARRAY_PRICE_RECURRING');
		foreach($aInfo['items'] as $iIndex => $aItem)
			if((int)$aInfo['client_id'] != 0 && (float)$aItem[$sKeyPriceSingle] == 0 && (float)$aItem[$sKeyPriceRecurring] == 0) {
				$aItemInfo = $this->callRegisterCartItem((int)$aItem['module_id'], array($aInfo['client_id'], $aInfo['vendor_id'], $aItem['id'], $aItem['quantity'], $this->_oConfig->getLicense()));
	            if(is_array($aItemInfo) && !empty($aItemInfo))
	            	$bProcessedFree = true;

	            $aInfo['items_count'] -= 1;
	            unset($aInfo['items'][$iIndex]);

	            $sCartItems = $this->_oDb->getCartItems($aInfo['client_id']);
	            $sCartItems = trim(preg_replace("'" . $this->_oConfig->descriptorA2S(array($aInfo['vendor_id'], $aItem['module_id'], $aItem['id'], $aItem['quantity'])) . ":?'", "", $sCartItems), ":");
	            $this->_oDb->setCartItems($aInfo['client_id'], $sCartItems);
			}

		if(empty($aInfo['items']))
            return $this->_sLangsPrefix . ($bProcessedFree ? 'msg_successfully_processed_free' : 'err_empty_order');

        $iPendingId = $this->_oDb->insertOrderPending($this->_iUserId, $sType, $sProvider, $aInfo);
        if(empty($iPendingId))
            return $this->_sLangsPrefix . 'err_access_db';

		/*
		 * Perform Join WITHOUT processing via payment provider
		 * if a client ISN'T logged in and has only ONE FREE item in the card.
		 */
		if((int)$aInfo['client_id'] == 0 && (int)$aInfo['items_count'] == 1) {
			reset($aInfo['items']);
			$aItem = current($aInfo['items']);

			if(!empty($aItem) && $this->_oConfig->getPrice($sType, $aItem)) {
				$this->_oDb->updateOrderPending($iPendingId, array(
		            'order' => $this->_oConfig->getLicense(),
		            'error_code' => '1',
		            'error_msg' => ''
		        ));

				$this->getObjectJoin()->performJoin($iPendingId);
			}
		}

		return $oProvider->initializeCheckout($iPendingId, $aInfo, $sRedirect);
	}

    public function actionFinalizeCheckout($sProvider, $mixedVendorId = "")
    {
        $aData = &$_REQUEST;

        $oProvider = $this->getObjectProvider($sProvider, $mixedVendorId);
        if($oProvider === false || !$oProvider->isActive())
        	return $this->_oTemplate->displayPageCodeError($this->_sLangsPrefix . 'err_incorrect_provider');

        $aResult = $oProvider->finalizeCheckout($aData);
        if((int)$aResult['code'] != BX_PAYMENT_RESULT_SUCCESS) 
        	return $this->_oTemplate->displayPageCodeError($aResult['message']);

		$aPending = $this->_oDb->getOrderPending(array('type' => 'id', 'id' => (int)$aResult['pending_id']));
		$bTypeRecurring = $aPending['type'] == BX_PAYMENT_TYPE_RECURRING;
		if($bTypeRecurring)
		    $this->registerSubscription($aPending, array(
		        'customer_id' => $aResult['customer_id'], 
		    	'subscription_id' => $aResult['subscription_id']
		    ));

		$this->onPaymentRegisterBefore($aPending);

		//--- Check "Pay Before Join" situation
		if((int)$aPending['client_id'] == 0)
			$this->getObjectJoin()->performJoin((int)$aPending['id'], isset($aResult['client_name']) ? $aResult['client_name'] : '', isset($aResult['client_email']) ? $aResult['client_email'] : '');

		//--- Register payment for purchased items in associated modules 
		if(!empty($aResult['paid']) || ($bTypeRecurring && !empty($aResult['trial'])))
			$this->registerPayment($aPending);

        bx_alert($this->getName(), 'finalize_checkout', 0, bx_get_logged_profile_id(), array(
            'pending' => $aPending,
            'transactions' => $this->_oDb->getOrderProcessed(array('type' => 'pending_id', 'pending_id' => (int)$aPending['id'])),
            'provider' => $oProvider,
            'message' => &$aResult['message'],
        ));

        if($oProvider->needRedirect()) {
			header('Location: ' . $oProvider->getReturnUrl());
			exit;
		}

		if(!empty($aResult['redirect'])) {
			header('Location: ' . base64_decode(urldecode($aResult['redirect'])));
			exit;
		}

		$this->_oTemplate->displayPageCodeResponse($aResult['message']);
    }

    public function actionFinalizedCheckout($sProvider, $mixedVendorId = "")
    {
        $oProvider = $this->getObjectProvider($sProvider, $mixedVendorId);
        if($oProvider === false || !$oProvider->isActive())
        	return $this->_oTemplate->displayPageCodeError($this->_sLangsPrefix . 'err_incorrect_provider');

        $aResult = $oProvider->finalizedCheckout();
        $this->_oTemplate->displayPageCodeResponse($aResult['message']);
    }

    public function actionNotify($sProvider, $mixedVendorId = "")
    {
    	$oProvider = $this->getObjectProvider($sProvider, $mixedVendorId);
        if($oProvider === false || !$oProvider->isActive())
        	return $this->_oTemplate->displayPageCodeError($this->_sLangsPrefix . 'err_incorrect_provider');

		$oProvider->notify();
    }

    public function onProfileJoin($iProfileId)
    {
    	$this->getObjectJoin()->onProfileJoin($iProfileId);
    }

    public function onProfileDelete($iProfileId)
    {
		$this->_oDb->onProfileDelete($iProfileId);
    }

    public function isAllowedSell($aItem, $bPerform = false)
    {
		$iUserId = (int)$this->getProfileId();
        if(!$iUserId)
        	return false;

		$aItemInfo = $this->callGetCartItem($aItem['module_id'], array($aItem['item_id']));
		if(empty($aItemInfo))
			return false;

        if(isAdmin())
            return true;

        $aCheckResult = checkActionModule($iUserId, 'sell', $this->getName(), $bPerform);
        if((int)$aItemInfo['author_id'] == $iUserId && $aCheckResult[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED)
			return true;

        return $aCheckResult[CHECK_ACTION_MESSAGE];
    }

	public function registerPayment($mixedPending)
    {
    	$aPending = is_array($mixedPending) ? $mixedPending : $this->_oDb->getOrderPending(array('type' => 'id', 'id' => (int)$mixedPending));
    	if(empty($aPending) || !is_array($aPending))
    		return false;

		$sType = $aPending['type'];
		$bTypeSingle = $sType == BX_PAYMENT_TYPE_SINGLE;

		if($bTypeSingle && (int)$aPending['processed'] == 1)
			return true;

		$iClientId = (int)$aPending['client_id'];
		$sLicense = $this->_oConfig->getLicense();

		$sCartItems = '';
		if($bTypeSingle)
			$sCartItems = $this->_oDb->getCartItems($iClientId);

		$bResult = false;
        $aItems = $this->_oConfig->descriptorsM2A($aPending['items']);
        foreach($aItems as $aItem) {
        	$sMethod = $bTypeSingle ? 'callRegisterCartItem' : 'callRegisterSubscriptionItem';
        	$aItemInfo = $this->$sMethod((int)$aItem['module_id'], array($aPending['client_id'], $aPending['seller_id'], $aItem['item_id'], $aItem['item_count'], $aPending['order'], $sLicense));
            if(empty($aItemInfo) || !is_array($aItemInfo))
                continue;

            $this->_oDb->insertOrderProcessed(array(
                'pending_id' => $aPending['id'],
                'client_id' => $aPending['client_id'],
                'seller_id' => $aPending['seller_id'],
                'module_id' => $aItem['module_id'],
                'item_id' => $aItem['item_id'],
                'item_count' => $aItem['item_count'],
                'amount' => $aItem['item_count'] * $this->_oConfig->getPrice($sType, $aItemInfo),
            	'license' => $sLicense,
            ));

            if($bTypeSingle)
            	$sCartItems = trim(preg_replace("'" . $this->_oConfig->descriptorA2S($aItem) . ":?'", "", $sCartItems), ":");

			$bResult = true;
        }

        if($bTypeSingle)
			$this->_oDb->setCartItems($iClientId, $sCartItems);

        if($bResult) {
        	$this->_oDb->updateOrderPending($aPending['id'], array('processed' => 1));

        	$this->onPaymentRegister($aPending);
        }

        return $bResult;
    }

	public function refundPayment($mixedPending)
	{
		$aPending = is_array($mixedPending) ? $mixedPending : $this->_oDb->getOrderPending(array('type' => 'id', 'id' => (int)$mixedPending));
    	if(empty($aPending) || !is_array($aPending))
    		return false;

		$bTypeSingle = $aPending['type'] == BX_PAYMENT_TYPE_SINGLE;

		$iCanceled = 0;
		$aOrders = $this->_oDb->getOrderProcessed(array('type' => 'pending_id', 'pending_id' => (int)$aPending['id']));
		foreach($aOrders as $aOrder) {
			$sMethod = $bTypeSingle ? 'callUnregisterCartItem' : 'callUnregisterSubscriptionItem';
			$bResult = $this->$sMethod((int)$aOrder['module_id'], array($aOrder['client_id'], $aOrder['seller_id'], $aOrder['item_id'], $aOrder['item_count'], $aPending['order'], $aOrder['license']));
			if(!$bResult)
                continue;

            if($this->_oDb->deleteOrderProcessed($aOrder['id']))
            	$iCanceled++;
		}

		if($iCanceled != count($aOrders))
			return false;

		$bResult = $this->_oDb->deleteOrderPending($aPending['id']);
		if($bResult)
			$this->onPaymentRefund($aPending);

		return $bResult;
	}

	public function registerSubscription($aPending, $aParams = array())
	{
	    $this->_oDb->insertSubscription(array(
	        'pending_id' => $aPending['id'],
	        'customer_id' => $aParams['customer_id'],
	        'subscription_id' => $aParams['subscription_id']
	    ));

	    $this->onSubscriptionCreate($aPending);
	}

	public function updateSubscription($aPending, $aParams = array())
	{
	    $this->_oDb->updateSubscription($aParams, array(
	        'pending_id' => $aPending['id']
	    ));

	    $this->onSubscriptionUpdate($aPending);
	}

	public function cancelSubscription($mixedPending)
	{
		$aPending = is_array($mixedPending) ? $mixedPending : $this->_oDb->getOrderPending(array('type' => 'id', 'id' => (int)$mixedPending));
    	if(empty($aPending) || !is_array($aPending) || $aPending['type'] != BX_PAYMENT_TYPE_RECURRING)
    		return false;

		$aItems = $this->_oConfig->descriptorsM2A($aPending['items']);
        foreach($aItems as $aItem)
			$this->callCancelSubscriptionItem((int)$aItem['module_id'], array($aPending['client_id'], $aPending['seller_id'], $aItem['item_id'], $aItem['item_count'], $aPending['order']));

		$this->onSubscriptionCancel($aPending);
	}

	public function onPaymentRegisterBefore($aPending, $aResult = array())
	{
		//--- 'System' -> 'Before Register Payment' for Alerts Engine ---//
		bx_alert('system', 'before_register_payment', 0, $aPending['client_id'], array('pending' => $aPending));
		//--- 'System' -> 'Before Register Payment' for Alerts Engine ---//
	}

	public function onPaymentRegister($aPending, $aResult = array())
	{
		//--- 'System' -> 'Register Payment' for Alerts Engine ---//
		bx_alert('system', 'register_payment', 0, $aPending['client_id'], array('pending' => $aPending));
		//--- 'System' -> 'Register Payment' for Alerts Engine ---//
	}

	public function onPaymentRefund($aPending, $aResult = array())
	{
		//--- 'System' -> 'Refund Payment' for Alerts Engine ---//
		bx_alert('system', 'refund_payment', 0, $aPending['client_id'], array('pending' => $aPending));
		//--- 'System' -> 'Refund Payment' for Alerts Engine ---//
	}

	public function onSubscriptionCreate($aPending, $aResult = array())
	{
		//--- 'System' -> 'Create Subscription' for Alerts Engine ---//
		bx_alert('system', 'create_subscription', 0, $aPending['client_id'], array('pending' => $aPending));
		//--- 'System' -> 'Create Subscription' for Alerts Engine ---//
	}

	public function onSubscriptionUpdate($aPending, $aResult = array())
	{
	    //--- 'System' -> 'Update Subscription' for Alerts Engine ---//
		bx_alert('system', 'update_subscription', 0, $aPending['client_id'], array('pending' => $aPending));
		//--- 'System' -> 'Update Subscription' for Alerts Engine ---//
	}
	
	public function onSubscriptionCancel($aPending, $aResult = array())
	{
		//--- 'System' -> 'Cancel Subscription' for Alerts Engine ---//
		bx_alert('system', 'cancel_subscription', 0, $aPending['client_id'], array('pending' => $aPending));
		//--- 'System' -> 'Cancel Subscription' for Alerts Engine ---//
	}

    public function setSiteSubmenu($sSubmenu, $sSelModule, $sSelName)
    {
        $oSiteSubmenu = BxDolMenu::getObjectInstance('sys_site_submenu');
        if(!$oSiteSubmenu)
            return;

        $sModuleSubmenu = $this->_oConfig->getObject($sSubmenu);
        $oModuleSubmenu = BxDolMenu::getObjectInstance($sModuleSubmenu);
        if(!$oModuleSubmenu) 
            return;

        $oSiteSubmenu->setObjectSubmenu($sModuleSubmenu);
        $oModuleSubmenu->setSelected($sSelModule, $sSelName);
    }
}

/** @} */
