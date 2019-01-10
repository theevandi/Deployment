<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

require_once('./inc/header.inc.php');

$sUploaderObject = bx_process_input(bx_get('uo'));
$sStorageObject = bx_process_input(bx_get('so'));
$sUniqId = bx_process_input(bx_get('uid'));
$isMultiple = bx_get('m') ? true : false;

$sFormat = bx_process_input(bx_get('f'));
if ($sFormat != 'html' &&  $sFormat != 'json')
    $sFormat = 'html';

$iContentId = bx_get('c');
if (false === $iContentId || '' === $iContentId)
    $iContentId = false;
else
    $iContentId = bx_process_input($iContentId, BX_DATA_INT);

if (!$sUploaderObject || !$sStorageObject || !$sUniqId)
    exit;

$isPrivate = (int)bx_get('p') ? 1 : 0;

$oUploader = BxDolUploader::getObjectInstance($sUploaderObject, $sStorageObject, $sUniqId);
if (!$oUploader) {
    // no such uploader available
    exit;
}

$sAction = bx_process_input(bx_get('a'));

switch ($sAction) {

    case 'show_uploader_form':
        header('Content-type: text/html; charset=utf-8');

        require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

        bx_import('BxDolLanguages');

        echo $oUploader->getUploaderForm($isMultiple, $iContentId, $isPrivate);
        break;

    case 'restore_ghosts':
        header('Content-type: text/html; charset=utf-8');
        $sImagesTranscoder = bx_process_input(bx_get('img_trans'));
        echo $oUploader->getGhosts(bx_get_logged_profile_id(), $sFormat, $sImagesTranscoder, $iContentId);
        break;

    case 'delete':
        header('Content-type: text/html; charset=utf-8');
        $iFileId = bx_process_input(bx_get('id'), BX_DATA_INT);
        echo $oUploader->deleteGhost($iFileId, bx_get_logged_profile_id());
        break;

    case 'upload':
        header('Content-type: text/html; charset=utf-8');

        bx_import('BxDolLanguages');

        $oUploader->handleUploads(bx_get_logged_profile_id(), isset($_FILES['f']) ? $_FILES['f'] : null, $isMultiple, $iContentId, $isPrivate);
        break;

}

/** @} */
