<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BasePayment Base classes for Payment like modules
 * @ingroup     UnaModules
 *
 * @{
 */

class BxBaseModPaymentCart extends BxDol
{
	protected $MODULE;
	protected $_oModule;

	function __construct()
    {
        parent::__construct();

        $this->_oModule = BxDolModule::getInstance($this->MODULE);
    }

    /**
     * @page service Service Calls
     * @section bx_base_payment Base Payment
     * @subsection bx_base_payment-integration Integration
     * @subsubsection bx_base_payment-get_cart_url get_cart_url
     * 
     * @code bx_srv('bx_payment', 'get_cart_url', [...], 'Cart'); @endcode
     * 
     * Get shopping cart URL.
     *
     * @param $iVendor (optional) integer value with vendor ID.
     * @return string with shopping cart URL.
     * 
     * @see BxBaseModPaymentCart::serviceGetCartUrl
     */
    /** 
     * @ref bx_base_payment-get_cart_url "get_cart_url"
     */
	public function serviceGetCartUrl($iVendor = 0)
    {
    	if(!$this->_oModule->isLogged())
            return '';

		if($iVendor == 0)
    		return $this->_oModule->_oConfig->getUrl('URL_CARTS');

    	return  bx_append_url_params($this->_oModule->_oConfig->getUrl('URL_CART'), array('seller_id' => $iVendor));
    }

    /**
     * @page service Service Calls
     * @section bx_base_payment Base Payment
     * @subsection bx_base_payment-integration Integration
     * @subsubsection bx_base_payment-get_cart_url get_cart_url
     * 
     * @code bx_srv('bx_payment', 'get_cart_url', [...], 'Cart'); @endcode
     * 
     * Get shopping cart URL.
     *
     * @param $iVendor (optional) integer value with vendor ID.
     * @return string with shopping cart URL.
     * 
     * @see BxBaseModPaymentCart::serviceGetCartUrl
     */
    /** 
     * @ref bx_base_payment-get_cart_url "get_cart_url"
     */
    public function serviceGetCartJs($sType = '', $iVendorId = 0)
    {
        return $this->_oModule->_oTemplate->displayCartJs($sType, $iVendorId);
    }

    /**
     * @page service Service Calls
     * @section bx_base_payment Base Payment
     * @subsection bx_base_payment-integration Integration
     * @subsubsection bx_base_payment-get_add_to_cart_js get_add_to_cart_js
     * 
     * @code bx_srv('bx_payment', 'get_add_to_cart_js', [...], 'Cart'); @endcode
     * 
     * Get JavaScript code to use in OnClick attributes. 
     *
     * @param $iVendor integer value with vendor ID.
     * @param $mixedModuleId mixed value (ID, Name or URI) determining a module from which the action was initiated.
     * @param $iItemId $iItemId integer value with item ID. 
     * @param $iItemCount integer value with a number of items for purchasing. 
     * @param $bNeedRedirect (optional) boolean value determining whether redirect is needed after add action or not.
     * @param $aCustom (optional) array with custom data to attach to an item added into a cart.
     * @return string with JavaScript code to use in OnClick attributes of HTML elements.
     * 
     * @see BxBaseModPaymentCart::serviceGetAddToCartJs
     */
    /** 
     * @ref bx_base_payment-get_add_to_cart_js "get_add_to_cart_js"
     */
    public function serviceGetAddToCartJs($iVendorId, $mixedModuleId, $iItemId, $iItemCount, $bNeedRedirect = false, $aCustom = array())
    {
		$iModuleId = $this->_oModule->_oConfig->getModuleId($mixedModuleId);
        if(empty($iModuleId))
            return '';

        return $this->_oModule->_oTemplate->displayAddToCartJs($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect, $aCustom);
    }

    /**
     * @page service Service Calls
     * @section bx_base_payment Base Payment
     * @subsection bx_base_payment-integration Integration
     * @subsubsection bx_base_payment-get_add_to_cart_link get_add_to_cart_link
     * 
     * @code bx_srv('bx_payment', 'get_add_to_cart_link', [...], 'Cart'); @endcode
     * 
     * Get HTML code for "Add to Cart" link. 
     *
     * @param $iVendorId integer value with vendor ID.
     * @param $mixedModuleId mixed value (ID, Name or URI) determining a module from which the action was initiated.
     * @param $iItemId $iItemId integer value with item ID. 
     * @param $iItemCount integer value with a number of items for purchasing. 
     * @param $bNeedRedirect (optional) boolean value determining whether redirect is needed after add action or not.
     * @param $aCustom (optional) array with custom data to attach to an item added into a cart.
     * @return HTML string with link to display on the site.
     * 
     * @see BxBaseModPaymentCart::serviceGetAddToCartLink
     */
    /** 
     * @ref bx_base_payment-get_add_to_cart_link "get_add_to_cart_link"
     */
	public function serviceGetAddToCartLink($iVendorId, $mixedModuleId, $iItemId, $iItemCount, $bNeedRedirect = false, $aCustom = array())
    {
        $iModuleId = $this->_oModule->_oConfig->getModuleId($mixedModuleId);
        if(empty($iModuleId))
            return '';

		return $this->_oModule->_oTemplate->displayAddToCartLink($iVendorId, $iModuleId, $iItemId, $iItemCount, $bNeedRedirect, $aCustom);
    }

    /**
     * @page service Service Calls
     * @section bx_base_payment Base Payment
     * @subsection bx_base_payment-integration Integration
     * @subsubsection bx_base_payment-get_cart_item_descriptor get_cart_item_descriptor
     * 
     * @code bx_srv('bx_payment', 'get_cart_item_descriptor', [...], 'Cart'); @endcode
     * 
     * Get cart item descriptor: 1-2-3-1. 
     *
     * @param $iVendorId integer value with vendor ID.
     * @param $iModuleId integer value with module ID determining a module from which the action was initiated.
     * @param $iItemId $iItemId integer value with item ID. 
     * @param $iItemCount integer value with a number of items for purchasing. 
     * @return string with item descriptor.
     * 
     * @see BxBaseModPaymentCart::serviceGetCartItemDescriptor
     */
    /** 
     * @ref bx_base_payment-get_cart_item_descriptor "get_cart_item_descriptor"
     */
	public function serviceGetCartItemDescriptor($iVendorId, $iModuleId, $iItemId, $iItemCount)
	{
		return $this->_oModule->_oConfig->descriptorA2S(array($iVendorId, $iModuleId, $iItemId, $iItemCount));
	}

	/**
     * @page service Service Calls
     * @section bx_base_payment Base Payment
     * @subsection bx_base_payment-integration Integration
     * @subsubsection bx_base_payment-get_cart_items_count get_cart_items_count
     * 
     * @code bx_srv('bx_payment', 'get_cart_items_count', [...], 'Cart'); @endcode
     * 
     * Get items count in member's shopping cart. 
     * 
     * @param $iUserId (optional) integer value with user ID. If empty value is provided then currently logged in user will be used.
     * @return integer value with items count.
     * 
     * @see BxBaseModPaymentCart::serviceGetCartItemsCount
     */
    /** 
     * @ref bx_base_payment-get_cart_items_count "get_cart_items_count"
     */
    public function serviceGetCartItemsCount($iUserId = 0)
    {
    	$iUserId = !empty($iUserId) ? $iUserId : $this->_oModule->getProfileId();
        if(empty($iUserId))
            return 0;

        $aInfo = $this->getInfo(BX_PAYMENT_TYPE_SINGLE, $iUserId);

        $iCount = 0;
        foreach($aInfo as $iVendorId => $aVendorCart)
            $iCount += $aVendorCart['items_count'];

        return $iCount;
    }

	protected function _parseByVendor($iUserId)
    {
        $sItems = $this->_oModule->_oDb->getCartItems($iUserId);
        return $this->_reparseBy($this->_oModule->_oConfig->descriptorsM2A($sItems), 'vendor_id');
    }

    protected function _parseByModule($iUserId)
    {
        $sItems = $this->_oModule->_oDb->getCartItems($iUserId);
        return $this->_reparseBy($this->_oModule->_oConfig->descriptorsM2A($sItems), 'module_id');
    }

	protected function _reparseBy($aItems, $sKey)
    {
        $aResult = array();
        foreach($aItems as $aItem)
            if(isset($aItem[$sKey]))
                $aResult[$aItem[$sKey]][] = $aItem;

        return $aResult;
    }
}

/** @} */
