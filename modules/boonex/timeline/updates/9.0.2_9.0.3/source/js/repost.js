/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Timeline Timeline
 * @ingroup     UnaModules
 *
 * @{
 */

function BxTimelineRepost(oOptions) {
	this._sActionsUri = oOptions.sActionUri;
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oTimelineRepost' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : oOptions.iOwnerId;
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._aHtmlIds = oOptions.aHtmlIds == undefined ? {} : oOptions.aHtmlIds;
    this._oRequestParams = oOptions.oRequestParams == undefined ? {} : oOptions.oRequestParams;
}

BxTimelineRepost.prototype = new BxTimelineMain();

BxTimelineRepost.prototype.repostItem = function(oLink, iOwnerId, sType, sAction, iId) {
	var $this = this;
	var oDate = new Date();
	var oParams = {
		owner_id: iOwnerId,
		type: sType,
		action: sAction,
		object_id: iId,
		_t: oDate.getTime()	
	};

	this.loadingIn(oLink, true);

	jQuery.post(
        this._sActionsUrl + 'repost/',
        oParams,
        function(oData) {
        	$this.loadingIn(oLink, false);

        	var oPopup = $(oLink).parents('.bx-popup-applied:visible:first');
        	if(oPopup.length >0)
        		oPopup.dolPopupHide();

        	var oCounter = $this._getCounter(oLink);
        	var bCounter = oCounter && oCounter.length > 0;
        	
        	if(oData && oData.msg != undefined && oData.msg.length > 0 && !bCounter)
                alert(oData.msg);
        	
        	if(oData && oData.counter != undefined && bCounter) {
        		var oCounterHolder = oCounter.parents('.' + $this.sSP + '-repost-counter-holder:first');

        		oCounter.replaceWith(oData.counter);
        		oCounterHolder.bx_anim(oData.count > 0 ? 'show' : 'hide');
        	}

        	if(oData && oData.disabled)
    			$(oLink).removeAttr('onclick').addClass($(oLink).hasClass('bx-btn') ? 'bx-btn-disabled' : $this.sSP + '-repost-disabled');
        },
        'json'
    );
};

BxTimelineRepost.prototype.toggleByPopup = function(oLink, iId) {
    var oData = this._getDefaultData();
    oData['id'] = iId;

	$(oLink).dolPopupAjax({
		id: this._aHtmlIds['by_popup'] + iId, 
		url: bx_append_url_params(this._sActionsUri + 'get_reposted_by/', oData)
	});

	return false;
};

BxTimelineRepost.prototype._getCounter = function(oElement) {
	var sSPRepost = this.sSP + '-repost';

	if($(oElement).hasClass(sSPRepost))
		return $(oElement).find('.' + sSPRepost + '-counter');
	else 
		return $(oElement).parents('.' + sSPRepost + ':first').find('.' + sSPRepost + '-counter');
};
