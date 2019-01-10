/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

/**
 * Simple Uploader js class
 */
function BxDolUploaderSimple (sUploaderObject, sStorageObject, sUniqId, options) {

    this.init(sUploaderObject, sStorageObject, sUniqId, options);

    this._sIframeId = 'bx-form-input-files-' + sUniqId + '-iframe';
    this._eForm = null;
}

BxDolUploaderSimple.prototype.init = function (sUploaderObject, sStorageObject, sUniqId, options) {
    this._isUploadsInProgress = false;

    this._sUploaderObject = sUploaderObject;
    this._sStorageObject = sStorageObject;

    this._sUniqId = sUniqId;

    this._sUploaderJsInstance = 'glUploader_' + sUniqId + '_' + this._sUploaderObject;
    this._sUploadInProgressContainerId = 'bx-form-input-files-' + sUniqId + '-upload-in-progress-' + this._sUploaderObject;
    this._sPopupContainerId = 'bx-form-input-files-' + sUniqId + '-popup-wrapper-' + this._sUploaderObject;

    this._sResultContainerId = 'bx-form-input-files-' + sUniqId + '-upload-result';
    this._sErrorsContainerId = 'bx-form-input-files-' + sUniqId + '-errors'; 

    this._sFormContainerId = 'bx-form-input-files-' + sUniqId + '-form-cont';

    this._sTemplateGhost = options.template_ghost ? options.template_ghost : '<div id="' + this._getFileContainerId('{file_id}') + '"><input type="hidden" name="f[]" value="{file_id}" />{file_name} (<a href="javascript:void(0);" onclick="{js_instance_name}.deleteGhost(\'{file_id}\')">delete</a>)</div>';
    this._sTemplateError = options.template_error_msg ? options.template_error_msg : '<div>{error}</div>' ;
    this._sTemplateErrorGhosts = options.template_error_ghosts ? options.template_error_ghosts : this._sTemplateError;

    this._isMultiple = undefined == options.multiple || !options.multiple ? false : true;

    this._iContentId = undefined == options.content_id || '' == options.content_id ? '' : parseInt(options.content_id);

    this._sImagesTranscoder = options.images_transcoder ? options.images_transcoder : '';

    this._isPrivate = undefined == options.storage_private || parseInt(options.storage_private) ? 1 : 0;

    this._isErrorShown = false;
}

BxDolUploaderSimple.prototype.isMultiple = function () {    
    return this._isMultiple;
}

BxDolUploaderSimple.prototype.getCurrentFilesCount = function () {
    return $('#' + $this._sResultContainerId + ' .bx-uploader-ghost').length;
}

BxDolUploaderSimple.prototype.showUploaderForm = function () {    
    var $this = this;
    var sUrl = this._getUrlWithStandardParams() + '&a=show_uploader_form&m=' + (this._isMultiple ? 1 : 0) + '&c=' + this._iContentId + '&p=' + this._isPrivate + '&_t=' + escape(new Date());

    $(window).dolPopupAjax({
        url: sUrl,
        id: {force: true, value: this._sPopupContainerId},
        onBeforeShow: function() {
            if ($this.isMultiple())
                $('#' + $this._sPopupContainerId + ' .bx-uploader-add-more-files').show();
            else
                $('#' + $this._sPopupContainerId + ' .bx-uploader-add-more-files').hide();
            $('#' + $this._sPopupContainerId + ' .bx-popup-element-close').click(function() {
                $this.onClickCancel();
            });
        },
        closeElement: false,
        closeOnOuterClick: false
    });
}

BxDolUploaderSimple.prototype.onClickCancel = function () {
    if (this._isUploadsInProgress) {
        if (confirm(_t('_sys_uploader_confirm_close_popup'))) {
            this.cancelAll();
            $('#' + this._sPopupContainerId).dolPopupHide({});
        }
    } else {
        $('#' + this._sPopupContainerId).dolPopupHide();
    }
}

BxDolUploaderSimple.prototype.onBeforeUpload = function (params) {

    this._eForm = params;

    this._loading(true, false);
        
    this._isUploadsInProgress = true;
    this._lockPageFromLeaving();
}

BxDolUploaderSimple.prototype.onProgress = function (params) {

}

BxDolUploaderSimple.prototype.onUploadCompleted = function (sErrorMsg) {

    this._isUploadsInProgress = false;
    this._unlockPageFromLeaving();
    this._loading(false, true);

    if (sErrorMsg.length) {
        this.restoreGhosts();        
        this._showError(sErrorMsg);
    } else {        
        this.restoreGhosts();
        $('#' + this._sPopupContainerId).dolPopupHide({});
    }
}

BxDolUploaderSimple.prototype.cancelAll = function () {
    $('#' + this._sIframeId).attr('src', 'javascript:false;');
    this.onUploadCompleted(_t('_sys_uploader_upload_canceled'));
}

BxDolUploaderSimple.prototype.restoreGhosts = function () {
    var sUrl = this._getUrlWithStandardParams() + '&img_trans=' + this._sImagesTranscoder + '&a=restore_ghosts&f=json' + '&c=' + this._iContentId + '&_t=' + (new Date());
    var $this = this;

    bx_loading(this._sResultContainerId, true);

    $.getJSON(sUrl, function (aData) {

        bx_loading($this._sResultContainerId, false);

        if (!$this.isMultiple())
            $('#' + $this._sResultContainerId + ' .bx-uploader-ghost').remove();

        if ('object' === typeof(aData)) {
            $.each(aData, function(iFileId, oVars) {
            	var oFileContainer = $('#' + $this._getFileContainerId(iFileId));
                if (oFileContainer.length > 0)
                    return;
                var sHTML;
                if (typeof $this._sTemplateGhost == 'object')
                    sHTML = $this._sTemplateGhost[iFileId];
                else
                    sHTML = $this._sTemplateGhost;
                for (var i in oVars)
                    sHTML = sHTML.replace (new RegExp('{'+i+'}', 'g'), oVars[i]);
                
                $('#' + $this._sResultContainerId).prepend(sHTML);

                oFileContainer.find('.bx-uploader-ghost-preview img').hide().fadeIn(1000);
            });
        }
    });
};

BxDolUploaderSimple.prototype.deleteGhost = function (iFileId) {
    var sUrl = this._getUrlWithStandardParams() + '&a=delete&id=' + iFileId;
    var $this = this;

    var sFileContainerId = $this._getFileContainerId(iFileId);
    bx_loading(sFileContainerId, true);

    $.post(sUrl, function (sMsg) {
        bx_loading(sFileContainerId, false);
        if ('ok' == sMsg) {
            $('#' + sFileContainerId).slideUp('slow', function () {
                $(this).remove();
            });
        } else {
            $('#' + this._sResultContainerId).prepend($this._sTemplateErrorGhosts.replace('{error}', sMsg));
        }        
    });
};

BxDolUploaderSimple.prototype._clearErrors = function () {
    $('#' + this._sPopupContainerId + ' #' + this._sErrorsContainerId).html('');
    this._isErrorShown = false;
}

BxDolUploaderSimple.prototype._showError = function (s, bAppend) {
    if (s == undefined || !s.length)
        return;
    if (!bAppend)
        $('#' + this._sPopupContainerId + ' #' + this._sErrorsContainerId).html(this._sTemplateError.replace('{error}', s));
    else
        $('#' + this._sPopupContainerId + ' #' + this._sErrorsContainerId).prepend(this._sTemplateError.replace('{error}', s));
    this._isErrorShown = true;
};

BxDolUploaderSimple.prototype._getFileContainerId = function (iFileId) {
	return 'bx-uploader-file-' + this._sStorageObject + '-' + iFileId;
};

BxDolUploaderSimple.prototype._getUrlWithStandardParams = function () {
    return sUrlRoot + 'storage_uploader.php?uo=' + this._sUploaderObject + '&so=' + this._sStorageObject + '&uid=' + this._sUniqId;
}

BxDolUploaderSimple.prototype._lockPageFromLeaving = function () {
    $(window).bind('beforeunload', function (e) {
        var e = e || window.event;
        // for ie, ff
        e.returnValue = _t('_sys_uploader_confirm_leaving_page');
        // for webkit
        return _t('_sys_uploader_confirm_leaving_page');
    });
}

BxDolUploaderSimple.prototype._unlockPageFromLeaving = function () {
    $(window).unbind('beforeunload');
}

BxDolUploaderSimple.prototype._loading = function (bShowProgress, bShowForm) {

    var eForm = $('#' + this._sFormContainerId + ' .bx-uploader-files-list');
    var eBtn = $('#' + this._sFormContainerId + ' .bx-btn-primary');

    if (bShowForm) {
        if (null != this._eForm) {
            eForm.find('.bx-uploader-simple-file').filter(':not(:first)').remove();
            if ('undefined' !== typeof(this._eForm.reset)) 
                this._eForm.reset();
        }
        eForm.show();
        eBtn.show();
    } else {
        eForm.hide();
        eBtn.hide();
    }

    bx_loading($('#' + this._sFormContainerId + ' .bx-uploader-loading').get(0), bShowProgress);
}

/**
 * HTML5 Uploader js class
 */
function BxDolUploaderHTML5 (sUploaderObject, sStorageObject, sUniqId, options) {

    this.init(sUploaderObject, sStorageObject, sUniqId, options);

    this._sIframeId = 'bx-form-input-files-' + sUniqId + '-iframe';
    this._eForm = null;    

    this._sDivId = 'bx-form-input-files-' + sUniqId + '-div-' + this._sUploaderObject;    

    this._uploader = null;

    this.initUploader = function (o) {

        var $this = this;

        if (null != this._uploader)
            this._uploader = null;

        var _options = {            
            element: $('#' + this._sDivId).get(0),
            action: sUrlRoot + 'storage_uploader.php',
            multiple: $this.isMultiple(),
            params: {
                uo: this._sUploaderObject,
                so: this._sStorageObject,
                uid: this._sUniqId,
                m: this._isMultiple ? 1 : 0,
                c: this._iContentId,
                p: this._isPrivate,
                a: "upload"
            },
            onSubmit: function(id, fileName){
                $this.onBeforeUpload('');
            },
            onProgress: function(id, fileName, loaded, total){
                $this.onProgress({
                    id: id, 
                    fileName: fileName, 
                    loaded: loaded, 
                    total: total
                });
            },
            onComplete: function(id, fileName, responseJSON){
                $this.onUploadCompleted('');
            },

            onCancel: function(id, fileName){
                $this.onUploadCompleted(_t('_sys_uploader_upload_canceled'));
            },

            messages: {
                onLeave: _t('_sys_uploader_confirm_leaving_page')
            },

            showMessage: function(message){ 
                $this._showError(message, true); 
            }

        };
        qq.extend(_options, o);
        this._uploader = new qq.FileUploader(_options);
    }

    this.onUploadCompleted = function (sErrorMsg) {        

        if (sErrorMsg.length)
            this._showError(sErrorMsg);        
        
        if (0 == this._uploader.getInProgress()) {
            this._isUploadsInProgress = false;
            this.restoreGhosts();
            if (!this._isErrorShown)
                $('#' + this._sPopupContainerId).dolPopupHide({});
        }
    }

    this.cancelAll = function () {
        this._uploader._handler.cancelAll();
        $('#' + this._sFormContainerId + ' .' + this._uploader._options.classes.list).html('');        
    }

    this.onBeforeUpload = function (params) {
        this._isUploadsInProgress = true;
    }

    this.onProgress = function (params) {

    }

}

BxDolUploaderHTML5.prototype = BxDolUploaderSimple.prototype;

/**
 * Crop Image Uploader js class
 */
function BxDolUploaderCrop (sUploaderObject, sStorageObject, sUniqId, options) {

    this.init(sUploaderObject, sStorageObject, sUniqId, options);

    this._eForm = null;

    this.initUploader = function (oOptions) {

        var $this = this;

        var aExt = ['jpg', 'jpeg', 'png', 'gif'];

        var eCroppie = $("#" + this._sFormContainerId + " .bx-croppie-container").croppie(oOptions);

		$("#" + this._sFormContainerId + ' .bx-crop-rotate').on('click', function(ev) {
			eCroppie.croppie('rotate', parseInt($(this).data('deg')));
		});
        
        $("#" + this._sFormContainerId + " input[name=f]").on("change", function() {
            var input = this;            

            if (input.files && input.files[0]) {

                var m = input.files[0].name.match(/\.([A-Za-z0-9]+)$/);

                if (2 != m.length || -1 == aExt.indexOf(m[1].toLowerCase())) {                    
                    $(input).replaceWith($(input).val('').clone(true));
                    $this._showError(_t('_sys_uploader_crop_wrong_ext'));
                    return;
                }

                $this._clearErrors();

                var reader = new FileReader()

                reader.onload = function(e) {
                    eCroppie.croppie('bind', {
                        url: e.target.result
                    });
                    $("#" + $this._sFormContainerId + " .bx-croppie-container").addClass('ready');
                    $("#" + $this._sFormContainerId + " .bx-crop-action").removeClass('bx-btn-disabled');
                    $("#" + $this._sFormContainerId + " .bx-croppie-container").data('bx-filename', input.files[0].name.replace(/(\.[A-Za-z0-9]+)$/, '.png'));
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

        $("#" + this._sFormContainerId + " .bx-crop-upload").on('click', function(ev) {
            eCroppie.croppie('result', {
                type: 'canvas',
                size: 'original'
            }).then(function(resp) {
                var fd = new FormData();

                fd.append("f", dataURItoBlob(resp), $("#" + $this._sFormContainerId + " .bx-croppie-container").data('bx-filename'));
                $.each(oOptions.bx_form, function (sName) {
                    fd.append(sName, this);
                });
                
                $this.onBeforeUpload(fd);

                $.ajax({
                    url: sUrlRoot + 'storage_uploader.php',
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function(data) {
                        eval(data);
                    },
                    error: function() {
                        $this._showError(_t('_sys_uploader_crop_err_upload'));
                    }
                })

            });
        });

        function dataURItoBlob(dataURI) {
            var split = dataURI.split(','),
                dataTYPE = split[0].match(/:(.*?);/)[1],
                binary = atob(split[1]),
                array = [];
                
            for (var i = 0; i < binary.length; i++) 
                array.push(binary.charCodeAt(i));

            return new Blob([new Uint8Array(array)], {
                type: dataTYPE
            });
        }

        
    };
}

BxDolUploaderCrop.prototype = BxDolUploaderSimple.prototype;

/** @} */
