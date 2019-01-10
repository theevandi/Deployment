﻿/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Attendant Attendant
 * @ingroup     UnaModules
 *
 * @{
 */

function BxAttendantPopupWithRecommendedOnProfileAdd(oOptions) {
    this._sContainerId = oOptions.sContainerId == undefined ? 'oBxAttendantPopupWithRecommendedOnProfileAdd' : oOptions.sContainerId;
    this._sUrlAfterShow = oOptions.sUrlAfterShow == undefined ? '' : oOptions.sUrlAfterShow;
    var $this = this;
    $(document).ready(function () {
        $this.init();
    });
}

BxAttendantPopupWithRecommendedOnProfileAdd.prototype.init = function () {
    var $this = this;
    $('.bx-pwropa-item-container').hide().first().show();
    $('#' + $this._sContainerId).dolPopup({ position: 'centered', closeOnOuterClick: false, onShow: $this.Show });
    $('.bx-pwropa-button').click(function () {
        $oCurr = $('.bx-pwropa-item-container:visible').hide();
        $oNext = $oCurr.next(); $oNext.show();
        $this.ReInitFlickity();
        if (!$oNext.length) {
            $('#' + $this._sContainerId).dolPopupHide();
            if ($this._sUrlAfterShow != '') {
                location.href = $this._sUrlAfterShow;
            }
        }
    });
}

BxAttendantPopupWithRecommendedOnProfileAdd.prototype.ReInitFlickity = function () {
    var $this = this;
    $('#' + $this._sContainerId + ' .bx-pwropa-item-container:visible .bx-base-unit-showcase-wrapper').flickity({
        cellSelector: '.bx-base-unit-showcase',
        cellAlign: 'left',
        pageDots: false,
        imagesLoaded: true
    });
    $('#' + $this._sContainerId)._dolPopupSetPosition();
}

BxAttendantPopupWithRecommendedOnProfileAdd.prototype.Show = function () {
    var $this = oBxAttendantPopupWithRecommendedOnProfileAdd;
    $this.ReInitFlickity();
}

BxAttendantPopupWithRecommendedOnProfileAdd.prototype.OnActionComplete = function (data, e) {
    if (data.err == false) {
        var $e = $(e);
        $e.hide();
    }
}

function bx_attendant_conn_action(e, sObj, sAction, iContentId, bConfirm, fOnComplete) {
    return bx_conn_action(e, sObj, sAction, iContentId, bConfirm, oBxAttendantPopupWithRecommendedOnProfileAdd.OnActionComplete)
}
