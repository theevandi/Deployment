/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

function processJsonData(oData) {
	var fContinue = function(oData) {
		if(oData && oData.reload != undefined && parseInt(oData.reload) == 1)
	    	document.location = document.location;

	    if(oData && oData.redirect != undefined && oData.redirect.length != 0)
	    	document.location = oData.redirect;

	    if(oData && oData.popup != undefined) {
	    	var oPopup = null;
	    	var oOptions = {
	            fog: {
					color: '#fff',
					opacity: .7
	            },
	            closeOnOuterClick: false
	        };

	    	if(typeof(oData.popup) == 'object') {
	    		oOptions = $.extend({}, oOptions, oData.popup.options);
	    		oPopup = $(oData.popup.html);
	    	}
	    	else 
	    		oPopup = $(oData.popup);

            if ('undefined' !== typeof(bx_editor_remove_all))
                bx_editor_remove_all($('#' + oPopup.attr('id')));
            
	    	$('#' + oPopup.attr('id')).remove();
	        oPopup.hide().prependTo('body').bxProcessHtml().dolPopup(oOptions);
	    }

	    if(oData && oData.form != undefined && oData.form_id != undefined)
	    	$('form#' + oData.form_id).replaceWith(oData.form).bxProcessHtml();

	    if (oData && oData.eval != undefined)
	        eval(oData.eval);
	};

	if(oData && oData.object != undefined && oData.grid != undefined)
		if(glGrids[oData.object] != undefined)
			glGrids[oData.object].processJson(oData);

	if(oData && oData.message != undefined && oData.message.length != 0)
		bx_alert(oData.message, function() {
			fContinue(oData);
		});
	else if(oData && oData.msg != undefined && oData.msg.length != 0)
		bx_alert(oData.msg, function() {
			fContinue(oData);
		});
	else
		fContinue(oData);
};

function getHtmlData( elem, url, callback, method , confirmation)
{
    var fPerform = function() {
		// in most cases it is element ID, in other cases - object of jQuery
		if (typeof elem == 'string')
		    elem = '#' + elem; // create selector from ID

		var $block = $(elem);

		var blockPos = $block.css('position');

		$block.css('position', 'relative'); // set temporarily for displaying "loading icon"

		bx_loading_content($block, true);
		var $loadingDiv = $block.find('.bx-loading-ajax');

		var iLeftOff = parseInt(($block.innerWidth() / 2.0) - ($loadingDiv.outerWidth()  / 2.0));
		var iTopOff  = parseInt(($block.innerHeight() / 2.0) - ($loadingDiv.outerHeight()));
		if (iTopOff<0) iTopOff = 0;

		$loadingDiv.css({
		    position: 'absolute',
		    left: iLeftOff,
		    top:  iTopOff
		});

		if (undefined != method && (method == 'post' || method == 'POST')) {
		    $.post(url, function(data) {
		        $block.html(data);
		        $block.css('position', blockPos).bxProcessHtml();

		        if (typeof callback == 'function')
		            callback.apply($block);
		    });		
		} 
		else {
		    $block.load(url + '&_r=' + Math.random(), function() {
		        $(this).css('position', blockPos).bxProcessHtml();

		        if (typeof callback == 'function')
		            callback.apply(this);
		    });
		}
    };

    if (typeof(confirmation) != 'undefined' && confirmation)
    	bx_confirm(_t('_Are_you_sure'), fPerform);
    else 
    	fPerform();
}

function loadDynamicBlockAutoPaginate (e, iStart, iPerPage, sAdditionalUrlParams) {

    sUrl = location.href;

    sUrl = sUrl.replace(/start=\d+/, '').replace(/per_page=\d+/, '').replace(/[&\?]+$/, '');
    sUrl = bx_append_url_params(sUrl, 'start=' + parseInt(iStart) + '&per_page=' + parseInt(iPerPage));
    if ('undefined' != typeof(sAdditionalUrlParams))
        sUrl = bx_append_url_params(sUrl, sAdditionalUrlParams);

    return loadDynamicBlockAuto(e, sUrl);
}

/**
 * This function reloads page block automatically, 
 * just provide any element inside the block and this function will reload the block.
 * @param e - element inside the block
 * @return true on success, or false on error - particularly, if block isn't found
 */
function loadDynamicBlockAuto(e, sUrl) {
	var oContainer = $(e).parents('.bx-page-block-container:first');
    var sId = oContainer.attr('id');

    if ('undefined' == typeof(sUrl))
        sUrl = location.href;

    if (!sId || !sId.length)
        return false;

    var aMatches = sId.match(/\-(\d+)$/);
    if (!aMatches || !aMatches[1])
        return false;

    var oPaginate = oContainer.find('.bx-paginate'); 
    if(oPaginate.length > 0) {
    	var iStart = parseInt(oPaginate.attr('bx-data-start'));
    	var iPerPage = parseInt(oPaginate.attr('bx-data-perpage'));

    	var oParams = {};
    	if(!isNaN(iStart) && sUrl.indexOf('start') == -1)
    		oParams.start = iStart;
    	if(!isNaN(iPerPage) && sUrl.indexOf('per_page') == -1)
    		oParams.per_page = iPerPage;

    	sUrl = bx_append_url_params(sUrl, oParams);
    }

    loadDynamicBlock(parseInt(aMatches[1]), sUrl);
    return true;
}

function loadDynamicBlock(iBlockID, sUrl) {
    
    var oCallback = null;
    if($('#bx-page-block-' + iBlockID + ' .bx-base-unit-showcase-wrapper').length){
        oCallback = bx_showcase_view_init;
    }
    getHtmlData($('#bx-page-block-' + iBlockID), bx_append_url_params(sUrl, 'dynamic=tab&pageBlock=' + iBlockID), oCallback);
    return true;
}

function loadDynamicPopupBlock(iBlockID, sUrl) {
    if (!$('#dynamicPopup').length) {
        $('<div id="dynamicPopup" style="display:none;"></div>').prependTo('body');
    }

    $('#dynamicPopup').load(
        (sUrl + '&dynamic=popup&pageBlock=' + iBlockID),
        function() {
            $(this).dolPopup({
                left: 0,
                top: 0
            });
        }
    );
}

function closeDynamicPopupBlock() {
    $('#dynamicPopup').dolPopupHide();
}


/**
 * Translate string
 */
function _t(s, arg0, arg1, arg2) {
    if (!window.aDolLang || !aDolLang[s])
        return s;

    cs = aDolLang[s];
    cs = cs.replace(/\{0\}/g, arg0);
    cs = cs.replace(/\{1\}/g, arg1);
    cs = cs.replace(/\{2\}/g, arg2);
    return cs;
}

function showPopupAnyHtml(sUrl, sId) {

    var oPopupOptions = {};

    if (!sId || !sId.length)
        sId = 'login_div';

    $('#' + sId).remove();
    $('<div id="' + sId + '" style="display: none;"></div>').prependTo('body').load(
        sUrl.match('^http[s]{0,1}:\/\/') ? sUrl : sUrlRoot + sUrl,
        function() {
            $(this).dolPopup(oPopupOptions);
        }
    );
}

function bx_loading_svg(sType, sClass) {
	sClass = sClass != undefined && sClass.length > 0 ? sClass : '';

	if(sUseSvgLoading != undefined && sUseSvgLoading.length > 0)
		return sUseSvgLoading.replace(new RegExp('__type__','g'), sType).replace(new RegExp('__class__','g'), sClass);

	var s = '';
	s += '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 520 520" class="' + sClass + '" style="enable-background:new 0 0 520 520;" xml:space="preserve">';
	s += '<g class="' + sType + '">';
	s += '<g class="inner">';
	s += '<path class="p1" d="M152,260C152,260,152,260,152,260c0-59.8,48.4-108.4,108.1-108.4c43.3,0,80.9,25.6,97.9,62.4V107.3c-28-18.3-61.9-28.9-98.1-28.9C159.8,78.4,78.6,159.9,78.6,260c0,59.5,28.4,112.4,73.4,145.6V260z"/>';
	s += '<path class="p2" d="M368,114.4V260c0,59.8-48.4,108.4-108.1,108.4c-43.3,0-80.9-25.6-97.9-62.4v106.7c28,18.3,61.9,28.9,98.1,28.9c100.1,0,181.3-81.4,181.3-181.6C441.4,200.5,413,147.6,368,114.4z"/>';
	s += '</g>';
	s += '<g class="outer">';
	s += '<path class="p1" d="M146.9,106.7c-8.1-15-36.9-29.9-68.9-27.3v124.2C90,164.3,114.6,130.5,146.9,106.7z"/>';
	s += '<path class="p2" d="M373.1,413.3c8.1,15,36.7,29.9,68.9,27.3V316.4C429.8,355.7,405.4,389.5,373.1,413.3z"/>';
	s += '</g>';
	s += '</g>';
	s += '</svg>';
	return s;
}

function bx_loading_animate (e, aOptions) {
    e = $(e);
    if (!e.length)
        return false;
    if (e.find('.bx-sys-spinner').length)
        return false;
    return new Spinner(aOptions).spin(e.get(0));
}

function bx_loading_btn (oElement, bEnable) {
    var oButton = $(oElement);
    var sClassHeight = oButton.hasClass('bx-btn-small') ? 'bx-btn-small-height' : 'bx-btn-height';

    if (oButton.children('div').size())
        oButton = oButton.children('div').first();

    if(!bEnable)
    	oButton.find('.bx-loading-ajax-btn').remove();
    else if (!oButton.find('.bx-loading-ajax-btn').length)
    	oButton.append('<b class="bx-loading-ajax-btn ' + sClassHeight + '">' + (bUseSvgLoading ? bx_loading_svg('colored', sClassHeight) : '') + '</b>');

    if(!bUseSvgLoading)
    	bx_loading_animate(oButton.find('.bx-loading-ajax-btn'), aSpinnerSmallOpts);    
}

function bx_loading_content (oElement, bEnable, isReplace) {
    var oBlock = $(oElement);
    var oLoading = $('<div class="bx-loading-ajax bx-def-z-index-front">' + (bUseSvgLoading ? bx_loading_svg('colored') : '') + '</div>');
    
    if(!bEnable)
    	oBlock.find(".bx-loading-ajax").remove();
    else if(!oBlock.find('.bx-loading-ajax').length) {
        if('undefined' != typeof(isReplace) && isReplace)
            oBlock.html(oLoading.addClass('static'));
        else
            oBlock.append(oLoading);

        if(!bUseSvgLoading)
        	bx_loading_animate(oBlock.find('.bx-loading-ajax'), aSpinnerOpts);
    } 
}

function bx_loading (elem, b) {

    if (typeof elem == 'string')
        elem = '#' + elem;

    var block = $(elem);

    if (block.hasClass('bx-btn')) {
        bx_loading_btn (block, b);
        return;
    }

    if (1 == b || true == b) {

        bx_loading_content(block, b);

        e = block.find(".bx-loading-ajax");
        e.css('left', parseInt(block.width()/2.0 - e.width()/2.0));

        var he = e.outerHeight();
        var hc = block.outerHeight();

        if (block.css('position') != 'relative' && block.css('position') != 'absolute') {
            if (!block.data('css-save-position'))
                block.data('css-save-position', block.css('position'));
            block.css('position', 'relative');
        }

        if (hc > he) {
            e.css('top', parseInt(hc/2.0 - he/2.0));
        }

        if (hc < he) {
            if (!block.data('css-save-min-height'))
                block.data('css-save-min-height', block.css('min-height'));
            block.css('min-height', he);
            e.css('top', 0);
        }

    } else {

        if (block.data('css-save-position'))
            block.css('position', block.data('css-save-position'));

        if (block.data('css-save-min-height'))
            block.css('min-height', block.data('css-save-min-height'));

        bx_loading_content(block, b);

    }

}


/**
 * Center content with floating blocks.
 * sSel - jQuery selector of content to be centered
 * sBlockSel - jquery selector of blocks
 */
function bx_center_content (sSel, sBlockStyle, bListenOnResize) {

    var sId;
    if ($(sSel).parent().hasClass('bx-center-content-wrapper')) {
        sId = $(sSel).parent().attr('id');
    } else {
        sId = 'id' + (new Date()).getTime();
        $(sSel).wrap('<div id="'+sId+'" class="bx-center-content-wrapper"></div>');
    }

    var eCenter = $('#' + sId);
    var iAll = $('#' + sId + ' ' + sBlockStyle).size();
    var iWidthUnit = $('#' + sId + ' ' + sBlockStyle + ':first').outerWidth(true);
    var iWidthContainer = eCenter.innerWidth();           
    var iPerRow = parseInt(iWidthContainer/iWidthUnit);
    var iLeft = (iWidthContainer - (iAll > iPerRow ? iPerRow * iWidthUnit : iAll * iWidthUnit)) / 2;

    if (iWidthUnit > iWidthContainer)
        return;

    if ('undefined' != typeof(bListenOnResize) && bListenOnResize) {
        $(window).on('resize.bx-center-content', function () {
            bx_center_content(sSel, sBlockStyle);
        });
    }

    eCenter.css("padding-left", iLeft);
}

/**
 * Show pointer popup with menu from URL.
 * @param o - menu object name
 * @param e - element to show popup at
 * @param options - popup options
 * @param vars - additional GET variables
 */
function bx_menu_popup (o, e, options, vars) {
    var options = options || {};
    var vars = vars || {};
    var o = $.extend({}, $.fn.dolPopupDefaultOptions, {id: o, url: bx_append_url_params('menu.php', $.extend({o:o}, vars))}, options);
    if ('undefined' == typeof(e))
        e = window;
    $(e).dolPopupAjax(o);
}

/**
 * Show pointer popup with menu from existing HTML.
 * @param jSel - jQuery selector for html to show in popup
 * @param e - element to show popup at
 * @param options - popup options
 */
function bx_menu_popup_inline (jSel, e, options) {
    var options = options || {};
    var o = $.extend({}, $.fn.dolPopupDefaultOptions, options, {pointer:{el:$(e)}});
    if ($(jSel + ':visible').length) 
        $(jSel).dolPopupHide(); 
    else 
        $(jSel).dolPopup(o);
}

/**
 * Show pointer popup with menu from URL.
 * @param sObject - menu object name
 * @param oElement - element to show popup at
 * @param oOptions - popup options
 * @param oVars - additional GET variables
 */
function bx_menu_slide (sObject, oElement, sPosition, oOptions, oVars) {
	var oVars = oVars || {};
    var oOptions = oOptions || {};
    oOptions = $.extend({}, {parent: 'body', container: '.bx-sliding-menu-content'}, oOptions);

    var sId = '';
    var sIdSel = '';
    var sIdPfx = 'bx-sliding-menu-wrapper-';
    if(typeof(oOptions.id) != 'undefined')
    	switch(typeof(oOptions.id)) {
    		case 'string':
    			sId = sIdPfx + oOptions.id;
    			break;

    		case 'object':
    			sId = typeof(oOptions.id.force) != 'undefined' && oOptions.id.force ? oOptions.id.value : sIdPfx + oOptions.id.value;
    			break;
    	}
    else
    	sId = sIdPfx + parseInt(2147483647 * Math.random());

    sIdSel = '#' + sId;
    
    //--- If slider exists
    if ($(sIdSel).length) {
    	bx_menu_slide_inline (sIdSel, oElement, sPosition);
    	return;
    }
    //--- If slider doesn't exists
    else {
    	var oMenuLoading = $('#bx-sliding-menu-loading');
    	$('<div id="' + sId + '" style="display:none;">' + oMenuLoading.html() + '</div>').addClass(oMenuLoading.attr('class')).appendTo(oOptions.parent);

        var oLoading = $(sIdSel + ' .bx-sliding-menu-loading');
        bx_loading_content(oLoading, true, true);

        bx_menu_slide_inline (sIdSel, oElement, sPosition);
        
        var fOnLoad = function() {
        	bx_loading_content(oLoading, false);

        	$(sIdSel).bxProcessHtml().show();
        };

        $(sIdSel).find(oOptions.container).load(bx_append_url_params('menu.php', $.extend({o:sObject}, oVars)), function () {
            var f = function () {
            	if($(sIdSel).find('img').length > 0 && !$(sIdSel).find('img').get(0).complete)
            		$(sIdSel).find('img').load(fOnLoad);
            	else
            		fOnLoad();
            };
            setTimeout(f, 100); // TODO: better way is to check if item is animating before positioning it in the code where popup is positioning
        });
    }
    	
}

/**
 * Show sliding menu from existing HTML.
 * @param sMenu - jQuery object or selector for html to show in popup
 * @param e - element to click to open/close slider
 * @param sPosition - 'block' for sliding menu in blocks, 'site' - for sliding main menu
 */
function bx_menu_slide_inline (sMenu, e, sPosition) {
    var options = options || {};
    var eSlider = $(sMenu);    

    if ('undefined' == typeof(e))
        e = eSlider.data('data-control-btn');

    var eIcon = $(e).find('.sys-icon-a');    

    var fPositionElement = function (oElement) {
        eSlider.css({
            position: 'absolute',
            top: oElement.outerHeight(true),
            left: 0
        });
    };

    var fPositionBlock = function () {
        var eBlock = eSlider.parents('.bx-page-block-container');
        eSlider.css({
            position: 'absolute',
            top: eBlock.find('.bx-db-header').outerHeight(true),
            left: 0
            //width: eBlock.width() TODO: Commited to test. Remove if everything is OK.
        });
    };

    var fPositionSite = function () {
        var eToolbar = $('#bx-toolbar');
        eSlider.css({
            position: 'fixed',
            top: eToolbar.outerHeight(true) + (typeof iToolbarSubmenuTopOffset != 'undefined' ? parseInt(iToolbarSubmenuTopOffset) : 0),
            left: 0
        });
    };

    var fClose = function () {
        if (eIcon.length)
            (new Marka(eIcon[0])).set(eIcon.attr('data-icon-orig'));
        eSlider.slideUp()
    };

    var fOpen = function () {
        if (eIcon.length) {
            eIcon.attr('data-icon-orig', eIcon.attr('data-icon'));
            (new Marka(eIcon[0])).set('times');
        }

        if(typeof sPosition == 'object')
            fPositionElement(sPosition);
        else if(typeof sPosition == 'string' && sPosition == 'block')
            fPositionBlock();
        else
            fPositionSite();

        eSlider.slideDown();

        eSlider.data('data-control-btn', e);        
    };    
    
    if ('undefined' == typeof(sPosition))
        sPosition = 'block';

    if (eSlider.is(':visible')) {
        fClose();

        $(document).off('click.bx-sliding-menu touchend.bx-sliding-menu');
        $(window).off('resize.bx-sliding-menu');
    }
    else {
        bx_menu_slide_close_all_opened();

        $(document).off('click.bx-sliding-menu touchend.bx-sliding-menu');
        $(window).off('resize.bx-sliding-menu');

        fOpen();
        eSlider.find('a').each(function () {
            $(this).off('click.bx-sliding-menu');
            $(this).on('click.bx-sliding-menu', function () {
                fClose();
            });
        });

        setTimeout(function () {

            var iWidthPrev = $(window).width();
            $(window).on('resize.bx-sliding-menu', function () {
                if ($(this).width() == iWidthPrev)
                    return;
                iWidthPrev = $(this).width();
                bx_menu_slide_close_all_opened();
            });
 
            $(document).on('click.bx-sliding-menu touchend.bx-sliding-menu', function (event) {
                if ($(event.target).parents('.bx-sliding-menu, .bx-sliding-menu-main, .bx-popup-slide-wrapper, .bx-db-header').length || $(event.target).filter('.bx-sliding-menu-main, .bx-popup-slide-wrapper, .bx-db-header').length || e === event.target)
                    event.stopPropagation();
                else
                    bx_menu_slide_close_all_opened();
            });

        }, 10);
    }
}

/**
 * Close all opened sliding menus
 */
function bx_menu_slide_close_all_opened () {
    $('.bx-sliding-menu:visible, .bx-sliding-menu-main:visible, .bx-popup-slide-wrapper:visible').each(function () {
        bx_menu_slide_inline('#' + this.id);
    });
}

/*
 * Note. oData.count_old and oData.count_new are also available and can be checked or used in notification popup.  
 */
function bx_menu_show_live_update(oData) {
	var sSelectorAddon = '.bx-menu-item-addon';

	//--- Update Child Menu Item
	if(oData.mi_child) {
		var oMenuItem = $('.bx-menu-object-' + oData.mi_child.menu_object + ' .bx-menu-item-' + oData.mi_child.menu_item);
		var oMenuItemAddon = oMenuItem.find(sSelectorAddon);

		if(oMenuItemAddon.length > 0)
			oMenuItemAddon.html(oData.count_new);
		else
			oMenuItem.append(oData.code.replace('{count}', oData.count_new));

		//+++ Check for 0 value
		oMenuItemAddon = oMenuItem.find(sSelectorAddon);
		if(parseInt(oData.count_new) > 0)
			oMenuItemAddon.show();
		else
			oMenuItemAddon.hide();
	}

	//--- Update Parent Menu Item
	if(oData.mi_parent) {
		var oMenuItem = $('.bx-menu-object-' + oData.mi_parent.menu_object + ' .bx-menu-item-' + oData.mi_parent.menu_item);
		var oMenuItemAddon = oMenuItem.find(sSelectorAddon);

		var iSum = 0;
		$('.bx-menu-object-' + oData.mi_child.menu_object + ' .bx-menu-item .bx-menu-item-addon').each(function() {
			iValue = parseInt($(this).html());
			if(iValue && iValue > 0)
				iSum += iValue;
		});

		if(oMenuItemAddon.length > 0)
			oMenuItemAddon.html(iSum);
		else
			oMenuItem.append(oData.code.replace('{count}', iSum));

		//+++ Check for 0 value
		oMenuItemAddon = oMenuItem.find(sSelectorAddon);
		if(iSum > 0)
			oMenuItemAddon.show();
		else
			oMenuItemAddon.hide();
	}
}
/**
 * Set ACL level for specified profile
 * @param iProfileId - profile id to set acl level for
 * @param iAclLevel - acl level id to assign to a given rofile
 */
function bx_set_acl_level (iProfileId, iAclLevel, mixedLoadingElement) {

    if ('undefined' != typeof(mixedLoadingElement))
        bx_loading($(mixedLoadingElement), true);

    $.post(sUrlRoot + 'menu.php', {o:'sys_set_acl_level', 'profile_id': iProfileId, 'acl_level_id': iAclLevel}, function(data) {

        if ('undefined' != typeof(mixedLoadingElement))
            bx_loading($(mixedLoadingElement), false);

        if (data.length) {
            bx_alert(data);
        } else if ($(mixedLoadingElement).hasClass('bx-popup-applied')) {
            $(mixedLoadingElement).dolPopupHide().remove();
        }
    });
}

function validateLoginForm(eForm) {
    return true;
}

/**
 * convert <time> tags to human readable format
 * @param sLang - use sLang localization
 * @param isAutoupdate - for internal usage only
 */
function bx_time(sLang, isAutoupdate, sRootSel) {
    if ('undefined' == typeof(sRootSel))
        sRootSel = 'body';
    var iAutoupdate = 22*60*60; // autoupdate time in realtime if less than 22 hours
    var sSel = 'time';

    if ('undefined' == typeof(sLang)) {
        sLang = glBxTimeLang;
    } else {
        glBxTimeLang = sLang;
    }

    if ('undefined' != typeof(isAutoupdate) && isAutoupdate)
        sSel += '.bx-time-autoupdate';

    $(sRootSel).find(sSel).each(function () {
        var s;
        var sTime = $(this).attr('datetime');
        var iSecondsDiff = moment(sTime).unix() - moment().unix();
        if (iSecondsDiff < 0)
            iSecondsDiff = -iSecondsDiff;

        if (iSecondsDiff < $(this).attr('data-bx-autoformat'))
            s = moment(sTime).locale(sLang).fromNow(); // 'time ago' format
        else
            s = moment(sTime).locale(sLang).format($(this).attr('data-bx-format')); // custom format

        if (iSecondsDiff < iAutoupdate)
            $(this).addClass('bx-time-autoupdate');
        else
            $(this).removeClass('bx-time-autoupdate');

        $(this).html(s);
    });

    if ($('time.bx-time-autoupdate').size()) {
        setTimeout(function () {
            bx_time(sLang, true);
        }, 20000);
    }
}

(function($) {

    $.fn.bxTime = function() {
        bx_time(undefined, undefined, this);
        return this;
    };

    /**
     * process HTML which was added dynamicly on the page
     */ 
    $.fn.bxProcessHtml = function (oCallback) {
        var eElement = $(this);
        if ('undefined' !== typeof(eElement)) {
            // process js time
            eElement.bxTime();

            // process web forms
            if ($.isFunction($.fn.addWebForms))
	            eElement.addWebForms();
            
            // process animating icons
        	bx_activate_anim_icons();

            // process syntax hightlighing
            if ('undefined' !== typeof(Prism) && eElement.size())
                Prism.highlightAllUnder(eElement[0]);
        }
        if ('undefined' !== typeof(oCallback)) {
            oCallback(eElement);
        }

        if (typeof glOnProcessHtml !== 'undefined' && glOnProcessHtml instanceof Array) {
            for (var i = 0; i < glOnProcessHtml.length; i++)
                if (typeof glOnProcessHtml[i] === "function")
                    glOnProcessHtml[i](this);
        }
        
        return this;
    }

} (jQuery));


/**
 * Perform connections AJAX request. 
 * In case of error - it shows js alert with error message.
 * In case of success - the page is reloaded.
 * 
 * @param sObj - connections object
 * @param sAction - 'add', 'remove' or 'reject'
 * @param iContentId - content id, initiator is always current logged in user
 * @param bConfirm - show confirmation dialog
 */
function bx_conn_action(e, sObj, sAction, iContentId, bConfirm, fOnComplete) {
    var fPerform = function() {
		var aParams = {
		    obj: sObj,
		    act: sAction,
		    id: iContentId
		};
		var fCallback = function (data) {
		    bx_loading_btn(e, 0);
		    if ('object' != typeof(data))
		        return;
		    if (data.err) {
		        bx_alert(data.msg);
		    } else {
		        if ('function' == typeof(fOnComplete))
		            fOnComplete(data, e);
		        else if (!loadDynamicBlockAuto(e))
		            location.reload();
		        else
		            $('#bx-popup-ajax-wrapper-bx_persons_view_actions_more').remove();
		    }
		};
		
		bx_loading_btn(e, 1);
		
		$.ajax({
		    dataType: 'json',
		    url: sUrlRoot + 'conn.php',
		    data: aParams,
		    type: 'POST',
		    success: fCallback
		});
    };

    if (typeof(bConfirm) != 'undefined' && bConfirm)
    	bx_confirm(_t('_Are_you_sure'), fPerform);
    else
    	fPerform();
}

function bx_append_url_params (sUrl, mixedParams) {
    var sParams = sUrl.indexOf('?') == -1 ? '?' : '&';

    if(mixedParams instanceof Array) {
    	for(var i in mixedParams)
            sParams += i + '=' + mixedParams[i] + '&';
        sParams = sParams.substr(0, sParams.length-1);
    }
    else if(mixedParams instanceof Object) {
    	$.each(mixedParams, function(sKey, sValue) {
    		sParams += sKey + '=' + sValue + '&';
    	});
        sParams = sParams.substr(0, sParams.length-1);
    }
    else
        sParams += mixedParams;

    return sUrl + sParams;
}

function bx_search_on_type (e, n, sFormSel, sResultsContSel, sLoadingContSel, bSortResults) {
    if ('undefined' != typeof(e) && 13 == e.keyCode) {
        $(sFormSel).find('input[name=live_search]').val(0);
        $(sFormSel).submit();
        return false;
    }

    if ('undefined' != typeof(glBxSearchTimeoutHandler) && glBxSearchTimeoutHandler)
        clearTimeout(glBxSearchTimeoutHandler);

    glBxSearchTimeoutHandler = setTimeout(function () {
        bx_search (n, sFormSel, sResultsContSel, sLoadingContSel, bSortResults);
    }, 500);

    return true;
}

function bx_search (n, sFormSel, sResultsContSel, sLoadingContSel, bSortResults) {
    if ('undefined' == typeof(sLoadingContSel))
        sLoadingContSel = sResultsContSel;
    if ('undefined' == typeof(bSortResults))
        bSortResults = false;

    var sQuery = $('input', sFormSel).serialize();

    bx_loading($(sLoadingContSel), true);
    $.post(sUrlRoot + 'searchKeywordContent.php', sQuery, function(data) {
        bx_loading($(sLoadingContSel), false);
        if (bSortResults) {
            var aSortedUnits = $(data).find(".bx-def-unit-live-search").toArray().sort(function (a, b) {
                return b.getAttribute('data-ts') - a.getAttribute('data-ts');
            });
            data = '';
            $.each(aSortedUnits.slice(0, n), function (i, e) {
                data += e.outerHTML;
            });
        } 
        $(sResultsContSel).html(data).bxProcessHtml();
    });

    return false;
}

function on_filter_apply(e, sInputId, sFilterName)
{
	var oRegExp = new RegExp("[&]{0,1}" + sFilterName + "=.*");
    var s = ('' + document.location).replace(oRegExp, ''); // remove filter
    s = s.replace(/page=\d+/, 'page=1'); // goto 1st page
    if (e.checked && $('#' + sInputId).val().length > 2)
        s += (-1 == s.indexOf('?') ? '?' : '&') + sFilterName + '=' + $('#' + sInputId).val(); // append filter
    document.location = s;
}

function on_filter_key_up (e, sCheckboxId)
{
    if (13 == e.keyCode) {
        $('#' + sCheckboxId).click();
        return false;
    }
    else {
        $('#' + sCheckboxId).removeAttr('checked');
        return true;
    }
}

function on_copyright_click()
{
	oDate = new Date();

	$(document).dolPopupAlert({
		message: _t('_copyright', oDate.getFullYear())
	});
}

function bx_activate_anim_icons(sColor)
{
    if ('undefined' == typeof(sColor))
        sColor = glBxAnimIconColor;
    else
       glBxAnimIconColor = sColor;

    $('.sys-icon-a').not('.marka').each(function () {
        var e = $(this);
        var m = new Marka(e.get(0)), r = e.attr('data-rotate'), c = e.attr('data-color');
        m.set(e.attr('data-icon')).rotate(r ? r : 'down').color(c ? c : sColor);
    });
}

function bx_get_style(sUrl)
{
	$("head").append($("<link rel='stylesheet' href='" + sUrl + "' type='text/css' />"));
}

function bx_get_param(s) {
    if (!window.aDolOptions || !aDolOptions[s])
        return false;

    return aDolOptions[s];
}

function bx_autocomplete_fields(iId, sUrl, sName, bShowImg, bOnlyOnce, onSelect){
	if (!$('#' + iId).hasClass('bx-form-input-autotoken'))
			$('#' + iId).addClass('bx-form-input-autotoken');
	
	$('#' + iId + ' input[type=text]').autocomplete({
		source: sUrl,
		select: function(e, ui) {
			$(this).val(ui.item.label);
			$(this).trigger('superselect', ui.item);
					
			if (typeof onSelect === 'function')
				onSelect(ui);
			
			e.preventDefault();			
		}
	});
	if ($('#' + iId + ' .val').length > 0 && bOnlyOnce)
		$('#' + iId + ' input[type=text]').hide();
	$('#' + iId + ' input[type=text]').on('superselect', function(e, item) {
		$(this).hide();
		if ('undefined' != typeof(item)) {
			var sImage = '';
			if(bShowImg && item.unit != undefined)
				sImage = item.unit;
			else if(bShowImg && item.thumb != undefined)
				sImage = '<img class="bx-def-thumb bx-def-thumb-size bx-def-margin-sec-right" src="' + item.thumb + '">';

			$(this).before('<b class="bx-def-color-bg-hl bx-def-round-corners">' + sImage + item.label + '<input type="hidden" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="' + sName + (bOnlyOnce ? '' : '[]') + '" value="' + item.value + '" /></b>');
		}

		if (!bOnlyOnce)
		{
			fAutoShrinkInput();
			$(this).show();
			this.value = '';
		}

	}).on('keydown', function(e) {

		// if: comma,enter (delimit more keyCodes with | pipe)
		if (/(13)/.test(e.which))
			e.preventDefault();

	});

	$('#' + iId).on('click', 'b', function() {
		$(this).remove();
			
		if (bOnlyOnce)
			$('#' + iId + ' input[type=text]').val('').show();
		else
			fAutoShrinkInput();
	});

	fAutoShrinkInput = function () {
		var iWidthCont = $('#' + iId + '.bx-form-input-autotoken').innerWidth();
		var iWidthExisting = 0;
		$('#' + iId + '.bx-form-input-autotoken b').each(function () {
			iWidthExisting += $(this).outerWidth(true);
		});

		$('#' + iId + '.bx-form-input-autotoken input').innerWidth(parseInt(iWidthCont - iWidthExisting > 180 ? iWidthCont - iWidthExisting : 180) - 5);
	};

	if (!bOnlyOnce)
		fAutoShrinkInput();
	else
		$('#' + iId + '.bx-form-input-autotoken input').outerWidth('100%');
};

function bx_alert(sMessage, fOnClickOk)
{
	$(document).dolPopupAlert({
		message: sMessage,
		onClickOk: fOnClickOk
	});
}

function bx_confirm(sMessage, fOnClickYes, fOnClickNo)
{
	$(document).dolPopupConfirm({
		message: sMessage,
		onClickYes: fOnClickYes,
		onClickNo: fOnClickNo
	});
}

function bx_prompt(sMessage, sValue, fOnClickOk, fOnClickCancel)
{
	$(document).dolPopupPrompt({
		message: sMessage,
		value: sValue,
		onClickOk: fOnClickOk,
		onClickCancel: fOnClickCancel
	});
}

function bx_get_scripts (aFiles, fCallback) 
{
    var 
        iLength = aFiles.length,
        aDeferreds = [],
        iCounter = 0,
        i = 0,
        fHandler = function() {
            if (iCounter++ >= (iLength-1)) {
                fCallback && fCallback();
            } 
            else if ('undefined' === typeof(glBxLoadedScripts[aFiles[iCounter]])) {
                glBxLoadedScripts[aFiles[iCounter]] = aFiles[iCounter];
                $.bxGetCachedScript(aFiles[iCounter]).done(fHandler);
            }
        };

    if ('undefined' === typeof(glBxLoadedScripts))
        glBxLoadedScripts = {}; 
    
    $.bxGetCachedScript(aFiles[iCounter]).done(fHandler);
}

jQuery.bxGetCachedScript = function(sUrl, oOptions) 
{ 
    oOptions = $.extend(oOptions || {}, {
        dataType: "script",
        cache: true,
        url: sUrl
    });

    return jQuery.ajax(oOptions);
};

/** @} */
