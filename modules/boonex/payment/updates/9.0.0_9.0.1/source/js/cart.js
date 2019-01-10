function BxPaymentCart(oOptions) {
	this.init(oOptions);
}

BxPaymentCart.prototype = new BxPaymentMain();

BxPaymentCart.prototype.init = function(oOptions) {
	if($.isEmptyObject(oOptions))
		return;

	this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oPmtCart' : oOptions.sObjName;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
};

BxPaymentCart.prototype.addToCart = function(iSellerId, iModuleId, iItemId, iItemCount, iNeedRedirect) {
	var $this = this;
    var oDate = new Date();

    iNeedRedirect = parseInt(iNeedRedirect);
    if(!iNeedRedirect)
    	iNeedRedirect = 0;

    $.post(
        this._sActionsUrl + 'add_to_cart/' + iSellerId + '/' + iModuleId + '/' + iItemId + '/' + iItemCount + '/',
        {
            _t:oDate.getTime()
        },
        function(oData){
        	$this.processResult(oData);

            if(oData.code == 0) {
            	/*
            	 * TODO: Display counter somewhere if it's needed.
            	 * 
            	$('#bx-menu-object-sys_account_notifications').html(oData.total_quantity);
                $('#bx-payment-tbar-content').replaceWith(oData.content);
                */

            	if(iNeedRedirect == 1)
            		window.location.href = sUrlRoot + 'cart.php';
            }
        },
        'json'
    );
};

BxPaymentCart.prototype.onCartContinue = function(oData) {
	if (!oData || oData.link == undefined)
		return;

	location.href = oData.link;
};

BxPaymentCart.prototype.onCartCheckout = function(oData) {
	if (!oData || oData.link == undefined)
		return;

	location.href = oData.link;
};

BxPaymentCart.prototype.subscribe = function(iSellerId, sSellerProvider, iModuleId, iItemId, iItemCount) {
    var $this = this;
    var oDate = new Date();

    var oParams = {
    	seller_id: iSellerId,
    	seller_provider: sSellerProvider,
    	module_id: iModuleId,
    	item_id: iItemId,
    	item_count: 1,
    	_t: oDate.getTime()
    };

    if(iItemCount != undefined && iItemCount.length > 0)
    	oParams.item_count = parseInt(iItemCount);

    $.post(
        this._sActionsUrl + 'subscribe_json/',
        oParams,
        function(oData){
        	$this.processResult(oData);
        },
        'json'
    );
};

BxPaymentCart.prototype.onSubscribeSubmit = function(oData) {
	document.location = oData.redirect;
};