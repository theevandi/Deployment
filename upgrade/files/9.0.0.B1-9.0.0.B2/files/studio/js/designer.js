/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentStudio Trident Studio
 * @{
 */
function BxDolStudioDesigner(oOptions) {
	this.sActionsUrl = oOptions.sActionUrl;
    this.sObjName = oOptions.sObjName == undefined ? 'oBxDolStudioDesigner' : oOptions.sObjName;
    this.sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this.iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this.sCodeMirror = oOptions.sCodeMirror == undefined ? '' : oOptions.sCodeMirror;

    var $this = this;
    $(document).ready (function () {
    	if($this.sCodeMirror != '')
    		$this.initCodeMirror($this.sCodeMirror);
    });
}

BxDolStudioDesigner.prototype.initCodeMirror = function(sSelector) {
	var oSelector = $(sSelector);
	for(var i = 0; i < oSelector.length; i++) {
	    var e = CodeMirror.fromTextArea(oSelector.get(i), {
	        lineNumbers: true,
	        mode: "htmlmixed",
	        htmlMode: true,
	        matchBrackets: true
	    });
	}
};

BxDolStudioDesigner.prototype.makeDefault = function(sUri) {
	var $this = this;
	var oDate = new Date();

	$.post(
		this.sActionsUrl,
		{
			dsg_action: 'make_default',
			dsg_value: sUri,
			_t: oDate.getTime()
		},
		function(oData) {
			if(oData.code != 0 && oData.message.length > 0) {
				alert(oData.message);
				return;
			}

			document.location.href = document.location.href; 
		},
		'json'
	);
};

BxDolStudioDesigner.prototype.deleteLogo = function() {
	var $this = this;
	var oDate = new Date();

	$.post(
		this.sActionsUrl,
		{
			dsg_action: 'delete_logo',
			_t: oDate.getTime()
		},
		function(oData) {
			if(oData.code != 0 && oData.message.length > 0) {
				alert(oData.message);
				return;
			}

			document.location.href = document.location.href; 
		},
		'json'
	);
};

BxDolStudioDesigner.prototype.deleteCover = function(sType, iId) {
	var $this = this;
	var oDate = new Date();

	$.post(
		this.sActionsUrl,
		{
			dsg_action: 'delete_cover',
			dsg_value: sType, 
			_t: oDate.getTime()
		},
		function(oData) {
			if(oData.code != 0 && oData.message.length > 0) {
				alert(oData.message);
				return;
			}

			$('#bx-dsg-cover-' + iId).bx_anim('hide', $this.sAnimationEffect, $this.iAnimationSpeed, function() {
				$(this).remove();
			});
		},
		'json'
	);
};
/** @} */
