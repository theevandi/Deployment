/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Timeline Timeline
 * @ingroup     UnaModules
 *
 * @{
 */

function BxTimelinePost(oOptions) {
	this._sActionsUri = oOptions.sActionUri;
    this._sActionsUrl = oOptions.sActionUrl;
    this._sObjName = oOptions.sObjName == undefined ? 'oTimelinePost' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
    this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
    this._aHtmlIds = oOptions.aHtmlIds == undefined ? {} : oOptions.aHtmlIds;
    this._sVideosAutoplay = oOptions.sVideosAutoplay == undefined ? 'off' : oOptions.sVideosAutoplay;
    this._oRequestParams = {
    	timeline: null,
    	outline: null,
    	general: oOptions.oRequestParams == undefined ? {} : oOptions.oRequestParams
    };

    this._sPregTag = "(<([^>]+bx-tag[^>]+)>)";
    this._sPregMention = "(<([^>]+bx-mention[^>]+)>)";
    this._sPregUrl = "\\b((https?://)|(www\\.))(([0-9a-zA-Z_!~*'().&=+$%-]+:)?[0-9a-zA-Z_!~*'().&=+$%-]+\\@)?(([0-9]{1,3}\\.){3}[0-9]{1,3}|([0-9a-zA-Z_!~*'()-]+\\.)*([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-zA-Z]\\.[a-zA-Z]{2,6})(:[0-9]{1,4})?((/[0-9a-zA-Z_!~*'().;?:\\@&=+$,%#-]+)*/?)";
    this._oAttachedLinks = {};

    var $this = this;
    if (typeof window.glOnInitEditor === 'undefined')
        window.glOnInitEditor = [];

    window.glOnInitEditor.push(function (sEditorSelector) {
        var sBaseUrl = sUrlRoot + 'plugins_public/emoji/js/';
        if ('undefined' === typeof(EmojiPicker)) {
                bx_get_scripts([
                    sBaseUrl + 'util.js', 
                    sBaseUrl + 'config.js',
                    sBaseUrl + 'emoji-picker.js', 
                    sBaseUrl + 'jquery.emojiarea.js'
                ], function() {
                    $this.initEmoji(sEditorSelector)
                });
            }
        else
            $this.initEmoji(sEditorSelector);
    });

    $(document).ready(function () {
        var oPost = $($this.sIdPost + ' form');
        if(!oPost || oPost.length == 0)
            oPost = $($this.sIdPostForm);

    	oPost.each(function() {
    		var sId = $(this).attr('id');
    		$this.initFormPost(sId);
    	});
    });
}

BxTimelinePost.prototype = new BxTimelineMain();

BxTimelinePost.prototype.initEmoji = function(sEditorSelector)
{
    var oLink = $(sEditorSelector).parents('form:first').find('a.add-emoji');

    var oEmojiConf = {
        emojiable_selector: bx_editor_get_htmleditable(sEditorSelector), 
            menu_icon: oLink  /* emoji popup menu icon */
    };
    new EmojiPicker(oEmojiConf ).discover();  /* call init emoji function */
};

BxTimelinePost.prototype.initFormPost = function(sFormId)
{
    var $this = this;
    var oForm = $('#' + sFormId);
    var oTextarea = oForm.find('textarea');

    autosize(oTextarea);

    oForm.ajaxForm({
        dataType: "json",
        beforeSubmit: function (formData, jqForm, options) {
        	window[$this._sObjName].beforeFormPostSubmit(oForm);
        },
        success: function (oData) {
        	window[$this._sObjName].afterFormPostSubmit(oForm, oData);
        }
    });

    if(typeof window.glOnSpaceEnterInEditor === 'undefined')
        window.glOnSpaceEnterInEditor = [];

    window.glOnSpaceEnterInEditor.push(function (sData, sSelector) {
        if(!oTextarea.is(sSelector))
            return;

        var oExp, aMatch = null;

        oExp = new RegExp($this._sPregTag , "ig");
        sData = sData.replace(oExp, '');

        oExp = new RegExp($this._sPregMention , "ig");
        sData = sData.replace(oExp, '');

        oExp = new RegExp($this._sPregUrl , "ig");
        while(aMatch = oExp.exec(sData)) {
            var sUrl = aMatch[0];
            if(!sUrl.length || $this._oAttachedLinks[sUrl] != undefined)
                continue;

            //--- Mark that 'attach link' process was started.
            $this._oAttachedLinks[sUrl] = 0;

            $this.addAttachLink(oForm, sUrl);
        }
    });
};

BxTimelinePost.prototype.beforeFormPostSubmit = function(oForm)
{
    this.loadingInButton($(oForm).children().find(':submit'), true);
};

BxTimelinePost.prototype.afterFormPostSubmit = function (oForm, oData)
{
	var $this = this;
	var fContinue = function() {
            if(oData && oData.form != undefined && oData.form_id != undefined) {
                $('#' + oData.form_id).replaceWith(oData.form);
                $this.initFormPost(oData.form_id);

                return;
            }

            if(oData && oData.id != undefined) {
                var iId = parseInt(oData.id);
                if(iId <= 0) 
                        return;

                if($('#' + $this._aHtmlIds['main_timeline']).length)
                        $this._getPost(oForm, iId, 'timeline', {afps_loading: 1});

                if($('#' + $this._aHtmlIds['main_outline']).length)
                        $this._getPost(oForm, iId, 'outline', {afps_loading: 1});
            }

            $this._getForm(oForm);
	};

    this.loadingInButton($(oForm).children().find(':submit'), false);

    if(oData && oData.message != undefined)
        bx_alert(oData.message, fContinue);
    else
        fContinue();
};

BxTimelinePost.prototype.initFormAttachLink = function(sFormId)
{
	var $this = this;
	var oForm = $('#' + sFormId);

	oForm.ajaxForm({
        dataType: "json",
        clearForm: true,
        beforeSubmit: function (formData, jqForm, options) {
        	window[$this._sObjName].beforeFormAttachLinkSubmit(oForm);
        },
        success: function (oData) {
        	window[$this._sObjName].afterFormAttachLinkSubmit(oForm, oData);
        }
    });
};

BxTimelinePost.prototype.beforeFormAttachLinkSubmit = function(oForm)
{
	this.loadingInButton($(oForm).children().find(':submit'), true);
};

BxTimelinePost.prototype.afterFormAttachLinkSubmit = function (oForm, oData)
{
	var $this = this;
	var fContinue = function() {
		if(oData && oData.item != undefined) {
			$('#' + $this._aHtmlIds['attach_link_popup']).dolPopupHide({onHide: function() {
				$(oForm).find('.bx-form-warn').hide();
			}});

			if(!$.trim(oData.item).length)
				return;

			var oItem = $(oData.item).hide();
			$('#' + $this._aHtmlIds['attach_link_form_field']).prepend(oItem).find('#' + oItem.attr('id')).bx_anim('show', $this._sAnimationEffect, $this._sAnimationSpeed, function() {
			});

	        return;
		}

		if(oData && oData.form != undefined && oData.form_id != undefined) {
			$('#' + oData.form_id).replaceWith(oData.form);
			$this.initFormAttachLink(oData.form_id);

			return;
		}
	};

	this.loadingInButton($(oForm).find(':submit'), false);

	if(oData && oData.message != undefined)
        bx_alert(oData.message, fContinue);
	else
		fContinue();
};

BxTimelinePost.prototype.deleteAttachLink = function(oLink, iId) {
	var $this = this;
    var oData = this._getDefaultData();
    oData['id'] = iId;

    var oAttachLink = $('#' + this._aHtmlIds['attach_link_item'] + iId);
    bx_loading(oAttachLink, true);
    
    jQuery.post (
        this._sActionsUrl + 'delete_attach_link/',
        oData,
        function(oData) {
        	var fContinue = function() {
        		if(oData && oData.code != undefined && oData.code == 0) {
        			for(var sUrl in $this._oAttachedLinks)
        				if(parseInt($this._oAttachedLinks[sUrl]) == parseInt(iId)) {
        					delete $this._oAttachedLinks[sUrl];
        					break;
        				}

            		oAttachLink.bx_anim('hide', $this._sAnimationEffect, $this._sAnimationSpeed, function() {
            			$(this).remove;
            		});
            	}
        	};

        	bx_loading(oAttachLink, false);

        	if(oData && oData.message != undefined)
                bx_alert(oData.message, fContinue);
        	else
        		fContinue();
        },
        'json'
    );

	return false;
};

BxTimelinePost.prototype.addAttachLink = function(oElement, sUrl) {
	if(!sUrl)
    	return;

    var $this = this;
    var oData = this._getDefaultData();
    oData['url'] = sUrl;

    jQuery.post (
	    this._sActionsUrl + 'add_attach_link/',
	    oData,
	    function(oData) {
	    	if(!oData.id || !oData.item || !$.trim(oData.item).length)
				return;

	    	//--- Mark that 'attach link' process was finished.
	    	$this._oAttachedLinks[sUrl] = oData.id;

			var oItem = $(oData.item).hide();
			$('#' + $this._aHtmlIds['attach_link_form_field']).prepend(oItem).find('#' + oItem.attr('id')).bx_anim('show', $this._sAnimationEffect, $this._sAnimationSpeed, function() {
			});
	    },
	    'json'
	);
};

BxTimelinePost.prototype.showAttachLink = function(oLink) {
	var oData = this._getDefaultData();    

    $(window).dolPopupAjax({
		id: {value: this._aHtmlIds['attach_link_popup'], force: true},
		url: bx_append_url_params(this._sActionsUri + 'get_attach_link_form/', oData),
		closeOnOuterClick: false
	});

	return false;
};

BxTimelinePost.prototype._getForm = function(oElement) {
    var $this = this;
    var oData = this._getDefaultData();

    jQuery.post (
	    this._sActionsUrl + 'get_post_form/',
	    oData,
	    function(oData) {
            if(oData && oData.form != undefined && oData.form_id != undefined) {
                $('#' + oData.form_id).replaceWith(oData.form);
                $this.initFormPost(oData.form_id);
            }
	    },
	    'json'
	);
};

BxTimelinePost.prototype._onGetPost = function(oData) {
	var $this = this;
	var fContinue = function(oData) {
		if(!$.trim(oData.item).length) 
			return;

		var oView = $('#' + $this._aHtmlIds['main_' + oData.view]);

		var oLoadMore = oView.find('.' + $this.sSP + '-load-more');
		if(!oLoadMore.is(':visible'))
			oLoadMore.show();

		var oEmpty = oView.find('.' + $this.sSP + '-empty');
		if(oEmpty.is(':visible'))
			oEmpty.hide();

		var oContent = $(oData.item).bxProcessHtml();
		switch(oData.view) {
			case 'timeline':
				var oTimeline = $('#' + $this._aHtmlIds['main_timeline']);
				var oItems = oTimeline.find('.' + $this.sClassItems);
				var oDivider  = oItems.find('.' + $this.sClassDividerToday);
				var bDivider = oDivider.length > 0;

				if(bDivider && !oDivider.is(':visible'))
					oDivider.show();

				oContent.hide();

				var oItem = bDivider ? oDivider.after(oContent).next('.' + $this.sClassItem + ':hidden') : oContent.prependTo(oItems);
				oItem.bx_anim('show', $this._sAnimationEffect, $this._iAnimationSpeed, function() {
					$(this).find('.bx-tl-item-text .bx-tl-content').checkOverflowHeight($this.sSP + '-overflow', function(oElement) {
						$this.onFindOverflow(oElement);
					});

					$this.initFlickity();
				});

				if($this._sVideosAutoplay != 'off')
					$this.initVideos(oTimeline);
				break;

			case 'outline':
				$this.prependMasonry(oContent, function(oItems) {
					$(oItems).find('.bx-tl-item-text .bx-tl-content').checkOverflowHeight($this.sSP + '-overflow', function(oElement) {
						$this.onFindOverflow(oElement);
					});

					$this.initFlickity();
				});
				break;
		}
	};

	if(oData && oData.message != undefined && oData.message.length != 0)
		bx_alert(oData.message, function() {
			fContinue(oData);
		});
	else
		fContinue(oData);	
};
