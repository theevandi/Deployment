<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

ob_start();

require_once('./inc/header.inc.php');

$sTranscoderObject = bx_process_input(bx_get('o'));
$sHandler = bx_process_input(bx_get('h'));

$oTranscoder = BxDolTranscoderImage::getObjectInstance($sTranscoderObject);

if (!$oTranscoder) {
    ob_end_clean();
    bx_transcoder_error_occured();
    exit;
}

ob_end_clean();

if (isset($_GET['dpx']))
    $oTranscoder->forceDevicePixelRatio((int)$_GET['dpx']);

if (!$oTranscoder->isFileReady($sHandler) && !$oTranscoder->transcode ($sHandler)) {
    bx_transcoder_error_occured();
    exit;
}

$sImageUrl = $oTranscoder->getFileUrl($sHandler);
if (!$sImageUrl) {
    bx_transcoder_error_occured();
    exit;
}

header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $sImageUrl);
exit;

function bx_transcoder_error_occured($sMethod = 'displayPageNotFound')
{
    require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");
    $oTemplate = BxDolTemplate::getInstance();
    $oTemplate->$sMethod ();
}

/** @} */
