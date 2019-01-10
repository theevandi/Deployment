/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

function bx_editor_insert_img (sEditorId, sImgId, sImgUrl, sClasses) {

    var eEditor = 'object' == typeof(tinymce) ? tinymce.get(sEditorId) : undefined;
    if ('undefined' == typeof(eEditor))
        return;

    if (eEditor.dom && eEditor.dom.doc && eEditor.dom.doc.getElementById(sImgId))
        return;

    if ('undefined' == typeof(sClasses))
        sClasses = '';

    eEditor.execCommand('mceInsertContent', false, '<img id="' + sImgId + '" class="' + sClasses + '" src="' + sImgUrl + '" />');
}

function bx_editor_remove_img (aEditorIds, aMarkers) {
    if ('object' != typeof(tinymce))
        return;

    var aEditors = [];
    for (var i = 0; i < aEditorIds.length; i++)
        aEditors.push(tinymce.get(aEditorIds[i]));

    for (var j = 0; j < aEditors.length; j++) {
        var eEditor = aEditors[j];
        if (typeof(eEditor) != 'object' || !eEditor.dom || !eEditor.dom.doc) 
        	continue;

        // delete images in html editor
        for (var k = 0; k < aMarkers.length; k++) {
            var jFiles = $(eEditor.dom.doc).find(aMarkers[k]);
            jFiles.each(function () { 
                eEditor.execCommand('mceRemoveNode', false, this); 
            });
        }
    }
}

/** @} */
