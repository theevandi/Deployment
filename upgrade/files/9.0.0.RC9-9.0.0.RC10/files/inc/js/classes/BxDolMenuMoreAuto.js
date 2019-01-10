/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

function BxDolMenuMoreAuto(options)
{
    this._sObject = options.sObject;
    this._aHtmlIds = undefined == options.aHtmlIds ? {} : options.aHtmlIds;

    this._sKeyWidth = 'bx-mma-width';

    this._sClassItem = '.bx-menu-item';
    this._sClassItemMore = this._sClassItem + '.bx-menu-item-more-auto';
    this._sClassItemMoreSubmenu = '.bx-menu-submenu-more-auto';

    var $this = this;
    $(document).ready(function () {
        $this.init();

        $(window).on('resize', function() {
           $this.update();
        });

        $($this._sClassItem).on('resize', function() {
            $this.update(true);
        });
    });
}

BxDolMenuMoreAuto.prototype.init = function() {
    var $this = this;

    $('.bx-menu-object-' + this._sObject).each(function() {
        var oMenu = $(this);
        var oItemMore = oMenu.find($this._sClassItemMore);
        var oItemMoreSubmenu = oItemMore.find($this._sClassItemMoreSubmenu);

        var iMenu = 0;
        oMenu.children($this._sClassItem + ':visible').each(function() {
            iMenu += $this._getWidth($(this));
        });

        var iParent = oMenu.parent().width();

        var iItemMore = oItemMore.outerWidth(true);

        oMenu.css('overflow', 'visible');

        if(iMenu < iParent)
            return;

        $this._moveToSubmenu(oMenu, oItemMore, oItemMoreSubmenu, iParent, iItemMore);
    });
};

BxDolMenuMoreAuto.prototype.update = function(bForceCalculate)
{
    var $this = this;

    $('.bx-menu-object-' + this._sObject).each(function() {
        var oMenu = $(this);
        var oItemMore = oMenu.find($this._sClassItemMore);
        var oItemMoreSubmenu = oItemMore.find($this._sClassItemMoreSubmenu);

        var iMenu = 0;
        oMenu.children($this._sClassItem + ':visible').each(function() {
            iMenu += $this._getWidth($(this), bForceCalculate);
        });

        var iParent = oMenu.parent().width();

        var iItemMore = oItemMore.outerWidth(true);

        if(iMenu > iParent)
            $this._moveToSubmenu(oMenu, oItemMore, oItemMoreSubmenu, iParent, iItemMore);
        if(iMenu < iParent)
            $this._moveFromSubmenu(oMenu, oItemMore, oItemMoreSubmenu, iParent, iMenu);
    });
};

BxDolMenuMoreAuto.prototype.more = function(oElement)
{
    var oElement = $(oElement);

    oElement.parents('li:first').find('#' + this._aHtmlIds['more_auto_popup']).dolPopup({
        pointer: {
            el: oElement
        }, 
        moveToDocRoot: false
    });
}

BxDolMenuMoreAuto.prototype._moveToSubmenu = function(oMenu, oItemMore, oItemMoreSubmenu, iParent, iItemMore)
{
    var $this = this;

    var bRelocateOthers = false;
    var iWidthTotal = iItemMore;
    var oSubmenuItemFirst = oItemMoreSubmenu.children(this._sClassItem + ':first');

    oMenu.children(this._sClassItem + ':not(' + this._sClassItemMore + ')').each(function() {
        var oItem = $(this);
        var iItem = $this._getWidth(oItem);
        if(bRelocateOthers || iWidthTotal + iItem > iParent) {
            if(!oSubmenuItemFirst.length)
                oItemMoreSubmenu.append(oItem.detach());
            else
                oSubmenuItemFirst.before(oItem.detach());
            bRelocateOthers = true;
            return;
        }

        iWidthTotal += iItem;
    });

    if(oItemMoreSubmenu.find('li').length)
        oItemMore.show();
   
};

BxDolMenuMoreAuto.prototype._moveFromSubmenu = function(oMenu, oItemMore, oItemMoreSubmenu, iParent, iMenu)
{
    var $this = this;

    var bStopRelocation = false;
    var iWidthTotal = iMenu;
    oItemMoreSubmenu.children(this._sClassItem).each(function() {
        if(bStopRelocation) 
            return;

        var oItem = $(this);
        var iItem = $this._getWidth(oItem);
        if(iWidthTotal + iItem > iParent) {
            bStopRelocation = true;
            return;
        }

        oItemMore.before(oItem.detach());
        iWidthTotal += iItem;
    });

    if(!oItemMoreSubmenu.find('li').length)
        oItemMore.hide();
};

BxDolMenuMoreAuto.prototype._getWidth = function(oItem, bForceCalculate)
{
    var iItem = parseInt(oItem.attr(this._sKeyWidth));
    if(!bForceCalculate && iItem)
        return iItem;

    iItem = oItem.outerWidth(true);
    if(iItem)
        oItem.attr(this._sKeyWidth, iItem);

    return iItem;
}
/** @} */
