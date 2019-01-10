/**
 * @package     Dolphin Core
 * @copyright   Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * @license     CC-BY - http://creativecommons.org/licenses/by/3.0/
 */

(function($) {

    $.fn.dolPopupDefaultOptions = {
        closeOnOuterClick: true,
        closeElement: '.bx-popup-element-close', // link to element which will close popup
        position: 'centered', // | 'absolute' | 'fixed' | event | element,
        fog: {color: '#fff', opacity: .7}, // {color, opacity},
        pointer: false, // {el:(string_id|jquery_object), align: (left|right|center)},
        left: 0, // only for fixed or absolute
        top: 0, // only for fixed
        onBeforeShow: function () {},
        onShow: function () {},
        onBeforeHide: function () {},
        onHide: function () {},
        speed: 150
    }; 

    $.fn.dolPopupDefaultPointerOptions = {
        align: 'right',
        offset: '0 0',
        offset_pointer: '0 0'
    }; 

    $.fn.dolPopup = function(options) {
        var options = options || {};
        var o = $.extend({}, $.fn.dolPopupDefaultOptions, options);

        if (false != o.pointer) {
            o.fog = false;
            o.pointer = $.extend({}, $.fn.dolPopupDefaultPointerOptions, $(document).data('bx-popup-options') ? $(document).data('bx-popup-options') : {}, o.pointer);
        }

        if (o.fog && !$('#bx-popup-fog').length) {
            $('<div id="bx-popup-fog" class="bx-def-z-index-overlay" style="display: none;">&nbsp;</div>')
                .prependTo('body');
        }

        $('#bx-popup-fog').css({
            position: 'fixed',
            top: 0,
            left: 0,
            width: $(window).width(),
            height: $(window).height(),
            opacity: o.fog.opacity,
            backgroundColor: o.fog.color
        });
        
        $(window).on('resize.popupFog', function () {
            $('#bx-popup-fog').css({
                width: $(window).width(),
                height: $(window).height()
            });
        });

        return this.each(function() {
            var $el = $(this);

            $el.addClass('bx-def-z-index-modal');

            // element must have id
            if (!$el.attr('id'))
                return false;

            if ($el.is(":visible")) { // if popup is shown - hide it
                $el.dolPopupHide();
                return true;
            } 

            // default style for correct positioning
            $el.css({
                display: 'block',
                visibility: 'hidden',
                position: 'absolute',
                top: 0,
                left: 0
            });

            setTimeout(function() { // timeout is needed for some browsers to render element
                $el._dolPopupSetPosition(o);

                if (!$el.hasClass('bx-popup-applied')) { // do this only once
                    $el.addClass('bx-popup-applied');

                    // save options for element
                    $el.data('bx-popup-options', o);

                    // attach event for "close element"
                    if (o.closeElement) {
                        $(o.closeElement, $el)
                            .css('cursor', 'hand')
                            .click(function() {
                                $el.dolPopupHide();
                            });
                    }

                    // attach event for outer click checking
                    if (o.closeOnOuterClick) {
                        var fCallback = function(e) {
                            if ($el.hasClass('bx-popup-applied') && $el.is(':visible')) {
                                if ($(e.target).parents('#' + $el.attr('id')).length == 0) {
                                    $el.dolPopupHide();
                                }
                            }

                            return true;
                        }

                        $(document).on({
                            click: fCallback,
                            touchend: fCallback
                        });
                    }
                }

                o.onBeforeShow();

                if (o.speed > 0) {
                    $el.css({display: 'none', visibility: 'visible'}).fadeIn(o.speed, o.onShow);
                    if (o.fog)
                        $('#bx-popup-fog').fadeIn(o.speed);
                } else {
                    $el.css({display: 'block', visibility: 'visible'});
                    if (o.fog)
                        $('#bx-popup-fog').show(o.onShow);
                }

            }, 10);
        });
    };

    $.fn.dolPopupHide = function(options) { 

        if ('undefined' == typeof(options) || 'object' != typeof(options))
            options = {};

        return this.each(function() {
            var $el = $(this);

            if (!$el.hasClass('bx-popup-applied'))
                return false;

            if (!$el.is(':visible') || 'hidden' == $el.css('visibility') || 'none' == $el.css('display'))
                return false;

            var o = $.extend({}, $el.data('bx-popup-options'), options);

            if (!o)
                return false;

            $(window).off('resize.popupWindow');
            $(window).off('resize.popupPointer');
            $(window).off('resize.popupFog');

            o.onBeforeHide();

            if (o.speed > 0) {                
                if (o.fog)
                    $('#bx-popup-fog').fadeOut(o.speed);
                $el.fadeOut(o.speed, o.onHide);
            } else {                
                if (o.fog)
                    $('#bx-popup-fog').hide();
                $el.hide(o.onHide);
            }
        });
    };


    $.fn.dolPopupAjax = function(options) { 
        
        if ('undefined' == typeof(options) || 'object' != typeof(options) || 'undefined' == typeof(options.url))
            return;

        if ('undefined' == typeof(options.container))
            options.container = '.bx-popup-content-wrapped';

        var bx_menu_on = function (e, b) {
            var li = $(e).parents('li:first');   
            if (!li.length)
                return;
            if (b) {
                var ul = $(e).parents('ul:first');   
                ul.find('li').removeClass('bx-menu-tab-active');
                li.addClass('bx-menu-tab-active');
            } else {
                li.removeClass('bx-menu-tab-active');
            }
        }

        var bx_menu_is_on = function (e) {    
            var li = $(e).parents('li:first');   
            if (!li.length)
                return false;
            return li.hasClass('bx-menu-tab-active');
        }

        return this.each(function() {
            var e = $(this);

            // get id
            var sPopupId = '';
            var sIdPrefix = 'bx-popup-ajax-wrapper-';
            if(typeof(e.attr('bx-popup-id')) != 'undefined')
            	sPopupId = sIdPrefix + e.attr('bx-popup-id');
            else if(typeof(options.id) != 'undefined')
            	switch(typeof(options.id)) {
            		case 'string':
            			sPopupId = sIdPrefix + options.id;
            			break;

            		case 'object':
            			sPopupId = typeof(options.id.force) != 'undefined' && options.id.force ? options.id.value : sIdPrefix + options.id.value;
            			break;
            	}
            else
            	sPopupId = sIdPrefix + parseInt(2147483647 * Math.random());

            var oPointerOptions = $.isWindow(e[0]) ? false : $.extend({}, {el:$(e), align:'center'}, options.pointer);
            if ($('#' + sPopupId + ':visible').length) { // if popup exists and is shown - hide it
                
                $('#' + sPopupId).dolPopupHide();

            } else if ($('#' + sPopupId).length) { // if popup exists but not shown - unhide it

                if (!$.isWindow(e[0]))
                    bx_menu_on(e, true);

                $('#' + sPopupId).dolPopup($.extend({}, options, {
                    pointer: oPointerOptions,
                    onHide: function () {
                        if (!$.isWindow(e[0]))
                            bx_menu_on(e, false);
                    }
                }));

            } else { // if popup doesn't exists - create new one from provided url

                if (!$.isWindow(e[0]))
                    bx_menu_on(e, true);

                $('<div id="' + sPopupId + '" style="display:none;">' + $('#bx-popup-loading').html() + '</div>').appendTo('body').find(options.container).hide();

                var oLoading = $('#' + sPopupId + ' .bx-popup-loading-wrapped');
                bx_loading_content(oLoading, true, true);

                $('#' + sPopupId).dolPopup($.extend({}, options, {
                    pointer: oPointerOptions,
                    onHide: function () {
                        if (!$.isWindow(e[0]))
                            bx_menu_on(e, false);
                    }
                }));

                var fOnLoad = function() {
                	bx_loading_content(oLoading, false);

                	$('#' + sPopupId + ' ' + options.container).bxTime().show();

					$('#' + sPopupId)._dolPopupSetPosition({
						pointer: oPointerOptions
					});
                };

                var sUrl = (options.url.indexOf('http://') == 0 || options.url.indexOf('https://') == 0 || options.url.indexOf('/') == 0 ? '' : sUrlRoot) + options.url;

                $('#' + sPopupId).find(options.container).load(sUrl, function () {
                	if($('#' + sPopupId).find('img').length > 0)
                		$('#' + sPopupId).find('img').load(fOnLoad);
                	else
                		fOnLoad();
                });
            }
        });
    };


    $.fn.dolPopupImage = function(sUrl, sLoadingElement) {

        return this.each(function() {

            var ePopup = $(this);
            var k = .95,
                w = $(window).width(),
                h = $(window).height(),
                eImg = new Image();

            if (!ePopup.find('img').size())
                return;

            ePopup.filter('.bx-popup-wrapper').addClass('bx-popup-image-wrapper');

            eImg.src = sUrl;

            if ('undefined' != typeof(sLoadingElement))
                bx_loading (sLoadingElement, true);

            $(eImg).on('load', function () {

                if ('undefined' != typeof(sLoadingElement))
                    bx_loading (sLoadingElement, false);

                ePopup.find('img').attr('src', sUrl);

                if ('undefined' != typeof(window.matchMedia) && window.matchMedia("(max-width:720px)").matches)
                    k = 1.0;

                // fit image into the browser window
                if (eImg.width < w * k && eImg.height < h * k) {
                    ePopup.find('img').css({
                        width: 'auto',
                        height: 'auto'
                    });
                }
                else if ((w * k - eImg.width) < (h * k - eImg.height)) {
                    ePopup.find('img').css({
                        width: '' + (eImg.width > w * k ? parseInt(w * k) : eImg.width) + 'px',
                        height: 'auto'
                    }).addClass('bx-constrain-width');
                } else {
                    ePopup.find('img').css({
                        width: 'auto',
                        height: '' + (eImg.height > h * k ? parseInt(h * k) : eImg.height) + 'px'
                    }).addClass('bx-constrain-height');
                }

                // show popup
                ePopup.dolPopup();

                // hide popup upon clicking/tapping on the image
                var fCallback = function (e) {
                    e.stopPropagation();
                    ePopup.dolPopupHide();
                };
                ePopup.find('img').on({
                    click: fCallback,
                    touchend: fCallback
                });

            });
        });
    };

    $.fn._dolPopupSetPosition = function(options) {

        var o = $.extend({}, $.fn.dolPopupDefaultOptions, options);

        if (undefined != o.pointer && false != o.pointer)
            o.pointer = $.extend({}, $.fn.dolPopupDefaultPointerOptions, $(document).data('bx-popup-options') ? $(document).data('bx-popup-options') : {}, o.pointer);

        return this.each(function() {
            var $el = $(this);
            
            if (o.pointer != false) {                
                
                var yOffset = 3;
                var ePointAt = 'string' == o.pointer.el ? $(o.pointer.el) : o.pointer.el;
                if (!ePointAt)
                    ePointAt = $('body');

                var aOffset = ('' + o.pointer.offset).split(' ', 2);
                var aOffsetPointer = ('' + o.pointer.offset_pointer).split(' ', 2);
                if (undefined == aOffset[0] || undefined == aOffset[1])
                    aOffset = [0, 0];
                if (undefined == aOffsetPointer[0] || undefined == aOffsetPointer[1])
                    aOffsetPointer = [0, 0];

                $el.position({
                    of: ePointAt,
                    my: o.pointer.align + '+' + parseInt(aOffset[0]) + ' top+' + yOffset + '+' + parseInt(aOffset[1] - 0),
                    at: o.pointer.align + ' bottom',
                    collision: 'flip none'
                });

                $el.find('.bx-popup-box-pointer').css('display', 'block').position({
                    of: ePointAt,
                    my: 'center+' + (parseInt(aOffsetPointer[0]) + parseInt(aOffset[0])) + ' top+' + yOffset + '+' + (parseInt(aOffsetPointer[1]) + parseInt(aOffset[1]) + 0),
                    at: 'center bottom'
                });

                $(window).on('resize.popupPointer', function() {
                    $el.position({
                        of: ePointAt,
                        my: o.pointer.align + '+' + parseInt(aOffset[0]) + ' top+' + yOffset + '+' + parseInt(aOffset[1]),
                        at: o.pointer.align + ' bottom',
                        collision: 'flip none'
                    });
                });

            } else if (o.position == 'fixed' || o.position == 'absolute') {

                $el.css({
                    position: o.position,
                    left: o.left,
                    top: o.top
                });

            } else if (o.position == 'centered') {

                $el.position({
                    of: window,
                    my: 'center center+' + ($el.outerHeight() > $(window).height() ? parseInt(($el.outerHeight() - $(window).height()) / 2) : '0'),
                    at: 'center center',
                    collision: 'none none'
                });

                // attach window resize event
                $(window).on('resize.popupWindow', function() {

                    $el.position({
                        of: window,
                        my: 'center center+' + ($el.outerHeight() > $(window).height() ? parseInt(($el.outerHeight() - $(window).height()) / 2) : '0'),
                        at: 'center center',
                        collision: 'none none'
                    });
                });

            } else if (typeof o.position == 'object') {

                $el.position({
                    of: o.position,
                    my: 'center top',
                    at: 'center bottom',
                    collision: 'flip flip'
                });

            }
        });
    };

})(jQuery);

