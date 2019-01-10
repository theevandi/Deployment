/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentStudio Trident Studio
 * @{
 */
function BxDolStudioModule(oOptions) {
	this.sActionsUrl = oOptions.sActionUrl;
    this.sObjName = oOptions.sObjName == undefined ? 'oBxDolStudioModule' : oOptions.sObjName;
    this.sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this.iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

BxDolStudioModule.prototype.activate = function(sName, oChecbox) {
	var $this = this;
	var oDate = new Date();
	var sAction = 'activate'; 

	$.get(
		this.sActionsUrl,
		{
			mod_action: sAction,
			mod_value: sName,
			_t:oDate.getTime()
		},
		function(oData) {
			if(oData.code != 0) {
				$('.bx-popup-applied:visible').dolPopupHide();

				$this.popup(sAction, oData.message);

				$(oChecbox).attr('checked', 'checked').trigger('enable');
				return;
			}

			var oBg = $('.bx-std-page-bg');
			var oContent = $('#bx-std-page-columns');
			if(oData.content.length > 0) {
				oBg.removeClass('bx-std-page-bg-empty');
				oContent.html(oData.content).bx_anim('show', $this.sAnimationEffect, $this.iAnimationSpeed);
			}
			else
				oContent.bx_anim('hide', $this.sAnimationEffect, $this.iAnimationSpeed, function() {
					$(this).html(oData.content);
					oBg.addClass('bx-std-page-bg-empty');
				});
		},
		'json'
	);
	return true;
};

BxDolStudioModule.prototype.popup = function(sType, sValue) {
	var sId = 'bx-std-mod-popup-' + sType;

    $('#' + sId).remove();
    $('<div id="' + sId + '" style="display: none;"></div>').prependTo('body').html(sValue);
    $('#' + sId).dolPopup({});
};
/** @} */
