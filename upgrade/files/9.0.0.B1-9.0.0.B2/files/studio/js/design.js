/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentStudio Trident Studio
 * @{
 */
function BxDolStudioDesign(oOptions) {
	this.sActionsUrl = oOptions.sActionUrl;
    this.sObjName = oOptions.sObjName == undefined ? 'oBxDolStudioDesign' : oOptions.sObjName;
    this.sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'fade' : oOptions.sAnimationEffect;
    this.iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this.sCodeMirror = oOptions.sCodeMirror == undefined ? '' : oOptions.sCodeMirror;

    var $this = this;
    $(document).ready (function () {
    	if($this.sCodeMirror != '')
    		$this.initCodeMirror($this.sCodeMirror);
    });

    var oCollapsable = $('#adm-settings-form .bx-form-collapsable');
    oCollapsable.on('bx_show', function() {
    	$this.refreshCodeMirror();
    });
}

BxDolStudioDesign.prototype.initCodeMirror = function(sSelector) {
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

BxDolStudioDesign.prototype.refreshCodeMirror = function() {
	$('.CodeMirror').each(function() {
	    this.CodeMirror.refresh();
	});
};

BxDolStudioDesign.prototype.activate = function(sTemplateName, oChecbox) {
	var $this = this;
	var oDate = new Date();

	$.get(
		this.sActionsUrl,
		{
			templ_action: 'activate',
			templ_value: sTemplateName,
			_t:oDate.getTime()
		},
		function(oData) {
			if(oData.code != 0) {
				alert(oData.message);

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
					$(this).html(oData.content)
					oBg.addClass('bx-std-page-bg-empty');
				});
		},
		'json'
	);
	return true;
};

/** @} */
