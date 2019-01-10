<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCoreSamples Samples
 * @{
 */

/**
 * @page samples
 * @section transcoder_audio Transcoder Audio
 *
 * This sample shows how transcoder can be used for audio files.
 *
 * @code

CREATE TABLE `sample_transcoder_audio_orig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mime_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ext` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

INSERT INTO `sys_objects_storage` (`object`, `engine`, `params`, `token_life`, `cache_control`, `levels`, `table_files`, `ext_mode`, `ext_allow`, `ext_deny`, `quota_size`, `current_size`, `quota_number`, `current_number`, `max_file_size`, `ts`) VALUES
('sample_transcoder_audio_orig', 'Local', '', 360, 2592000, 0, 'sample_transcoder_audio_orig', 'allow-deny', 'wav,mp3,m4a,aac,ogg', '', 0, 0, 0, 0, 0, 0);

CREATE TABLE `sample_transcoder_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL,
  `remote_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mime_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ext` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `added` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `remote_id` (`remote_id`)
);

INSERT INTO `sys_objects_storage` (`object`, `engine`, `params`, `token_life`, `cache_control`, `levels`, `table_files`, `ext_mode`, `ext_allow`, `ext_deny`, `quota_size`, `current_size`, `quota_number`, `current_number`, `max_file_size`, `ts`) VALUES
('sample_transcoder_audio', 'Local', '', 360, 2592000, 0, 'sample_transcoder_audio', 'allow-deny', 'wav,mp3,m4a,aac,ogg', '', 0, 0, 0, 0, 0, 0);

INSERT INTO `sys_objects_transcoder` (`object`, `storage_object`, `source_type`, `source_params`, `private`, `atime_tracking`, `atime_pruning`, `ts`, `override_class_name`, `override_class_file`) VALUES
('sample_audio_mp3', 'sample_transcoder_audio', 'Storage', 'a:1:{s:6:"object";s:28:"sample_transcoder_audio_orig";}', 'no', 0, 0, 0, 'BxDolTranscoderAudio', '');

INSERT INTO `sys_transcoder_filters` (`transcoder_object`, `filter`, `filter_params`, `order`) VALUES
('sample_audio_mp3', 'Mp3', 'a:2:{s:13:"audio_bitrate";i:96;s:10:"force_type";s:3:"mp3";}', 0);

 * @endcode
 *
 */

require_once('./../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

$oTemplate = BxDolTemplate::getInstance();
$oTemplate->setPageNameIndex (BX_PAGE_DEFAULT);
$oTemplate->setPageHeader ("Sample audio transcoder");
$oTemplate->setPageContent ('page_main_code', PageCompMainCode());
$oTemplate->getPageCode();

/**
 * page code function
 */
function PageCompMainCode()
{
    ob_start();

    $sStorageObjectOrig = 'sample_transcoder_audio_orig';
    $sTranscoderObject = 'sample_audio_mp3';
    $iProfileId = bx_get_logged_profile_id();

    if (!$iProfileId) {
        echo "You aren't logged in.";
        exit;
    }

    $iPrunedFiles = BxDolTranscoder::pruning();
    if ($iPrunedFiles) {
        echo "iPrunedFiles: $iPrunedFiles";
        exit;
    }
    $oTranscoder = BxDolTranscoderAudio::getObjectInstance($sTranscoderObject);
    if (!$oTranscoder) {
        echo "Transcoder object is not available: " . $sTranscoder;
        exit;
    }
    echo "registerHandlers mp3: [" . $oTranscoder->registerHandlers() . "] <br />\n";

    $oStorageOrig = BxDolStorage::getObjectInstance($sStorageObjectOrig);
    if (!$oStorageOrig) {
        echo "Storage object is not available: " . $sStorageObjectOrig;
        exit;
    }

    if (isset($_POST['upload'])) {
    
        $iId = $oStorageOrig->storeFileFromForm($_FILES['file'], true, $iProfileId);
        if ($iId) {
            $iCount = $oStorageOrig->afterUploadCleanup($iId, $iProfileId);
            echo "<h2>Uploaded file id: " . $iId . "(deleted ghosts:" . $iCount . ") </h2>";

            // force transcode
            echo "Force transcode: <br />";
            echo "mp3: " . $oTranscoder->getFileUrl($iId) . '<br />';
        } else {
            echo "<h2>Error uploading file: " . $oStorage->getErrorString() . '</h2><hr class="bx-def-hr" />';
        }

    }
    elseif (isset($_POST['delete'])) {

        foreach ($_POST['file_id'] as $iFileId) {
            $bRet = $oStorageOrig->deleteFile($iFileId, $iProfileId);
            if ($bRet)
                echo "<h2>Deleted file id: " . $iFileId . '</h2><hr class="bx-def-hr" />';
            else
                echo "<h2>File deleting error: " . $oStorageOrig->getErrorString() . '</h2><hr class="bx-def-hr" />';
        }

    } 
    else {

        $a = $oStorageOrig->getFilesAll();
        foreach ($a as $r) {
            $sUrlMP3 = $oTranscoder->getFileUrl($r['id']);

            echo '<h3>' . $r['file_name'] . '(' . $sUrlMP3 . ')</h3>';
            echo '<audio controls><source src="' . $sUrlMP3 . '" type="audio/mpeg" /></audio>';
            echo '<hr class="bx-def-hr" />';
        }
    }


    $a = $oStorageOrig->getFilesAll();
?>
    <h2>Files List</h2>
    <form method="POST">
        <?php foreach ($a as $r): ?>
            <input type="checkbox" name="file_id[]" value="<?=$r['id'] ?>" />
            <?=$r['file_name'] ?>
            <br />
        <?php endforeach; ?>
        <input type="submit" name="delete" value="Delete" class="bx-btn bx-btn-small bx-def-margin-sec-top" style="float:none;" />
    </form>
    <hr class="bx-def-hr" /> 


    <h2>Upload</h2>
    <form enctype="multipart/form-data" method="POST">
        <input type="file" name="file" />
        <br />
        <input type="submit" name="upload" value="Upload" class="bx-btn bx-btn-small bx-def-margin-sec-top" style="float:none;" />
    </form>
<?php

    $s = ob_get_clean();
    return DesignBoxContent("Sample audio transcoder", $s, BX_DB_PADDING_DEF);

}

/** @} */
