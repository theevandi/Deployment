<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Videos Videos
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Videos module
 */
class BxVideosModule extends BxBaseModTextModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    /**
     * @page service Service Calls
     * @section bx_videos Videos
     * @subsection bx_videos-page_blocks Page Blocks
     * @subsubsection bx_videos-entity_video_block entity_video_block
     * 
     * @code bx_srv('bx_videos', 'entity_video_block', [...]); @endcode
     * 
     * Get page block with video player.
     *
     * @param $iContentId (optional) video ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @return HTML string with block content to display on the site or false if there is no enough input data. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxVideosModule::serviceEntityVideoBlock
     */
    /** 
     * @ref bx_videos-entity_video_block "entity_video_block"
     */
    public function serviceEntityVideoBlock ($iContentId = 0)
    {
        $mixedContent = $this->_getContent($iContentId);
        if($mixedContent === false)
            return false;

        list($iContentId, $aContentInfo) = $mixedContent;

        return $this->_oTemplate->entryVideo($aContentInfo);
    }

	/**
     * @page service Service Calls
     * @section bx_videos Videos
     * @subsection bx_videos-page_blocks Page Blocks
     * @subsubsection bx_videos-entity_rating entity_rating
     * 
     * @code bx_srv('bx_videos', 'entity_rating', [...]); @endcode
     * 
     * Get page block with Stars based video's rating.
     *
     * @param $iContentId (optional) video ID. If empty value is provided, an attempt to get it from GET/POST arrays will be performed.
     * @return HTML string with block content to display on the site or false if there is no enough input data. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxVideosModule::serviceEntityRating
     */
    /** 
     * @ref bx_videos-entity_rating "entity_rating"
     */
    public function serviceEntityRating($iContentId = 0)
    {
    	return $this->_serviceTemplateFunc ('entryRating', $iContentId);
    }

    protected function _getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams = array())
    {
        $aResult = parent::_getContentForTimelinePost($aEvent, $aContentInfo, $aBrowseParams);

        if(!empty($aResult['videos']) && is_array($aResult['videos']))
            $aResult['images'] = array();

        return $aResult;
    }
    protected function _getImagesForTimelinePost($aEvent, $aContentInfo, $sUrl, $aBrowseParams = array())
    {
        list($sImageThumb, $sImageGallery, $sImageCover) = $this->_oTemplate->getUnitImages($aContentInfo);
        if(empty($sImageGallery) && !empty($sImageThumb))
            $sImageGallery = $sImageThumb;

        if(empty($sImageGallery))
            return array();

        return array(
            array('url' => $sUrl, 'src' => $sImageGallery, 'src_orig' => $sImageCover),
        );
    }

    protected function _getVideosForTimelinePost($aEvent, $aContentInfo, $sUrl, $aBrowseParams = array())
    {
        $CNF = &$this->_oConfig->CNF;

        if(empty($CNF['OBJECT_STORAGE_VIDEOS']) || empty($CNF['OBJECT_VIDEOS_TRANSCODERS']))
            return array();

        $iFile = (int)$aContentInfo[$CNF['FIELD_VIDEO']];
        $aFile = BxDolStorage::getObjectInstance($CNF['OBJECT_STORAGE_VIDEOS'])->getFile($iFile);
        if(empty($aFile) || !is_array($aFile) || strncmp('video/', $aFile['mime_type'], 6) !== 0)
            return array();

        $oTcvPoster = BxDolTranscoder::getObjectInstance($CNF['OBJECT_VIDEOS_TRANSCODERS']['poster']);
        $oTciPoster = BxDolTranscoder::getObjectInstance($CNF['OBJECT_IMAGES_TRANSCODER_POSTER']);
        $oTcvMp4 = BxDolTranscoder::getObjectInstance($CNF['OBJECT_VIDEOS_TRANSCODERS']['mp4']);
        $oTcvWebm = BxDolTranscoder::getObjectInstance($CNF['OBJECT_VIDEOS_TRANSCODERS']['webm']);
        if(!($oTcvPoster || $oTciPoster) || !$oTcvMp4 || !$oTcvWebm)
            return array();

        if(isset($CNF['FIELD_POSTER']) && !empty($aContentInfo[$CNF['FIELD_POSTER']]) && $oTciPoster)
            $sPoster = $oTciPoster->getFileUrl($aContentInfo[$CNF['FIELD_POSTER']]);
        else 
            $sPoster = $oTcvPoster->getFileUrl($iFile);

        return array(
            $iFile => array(
            	'id' => $iFile,
            	'src_poster' => $sPoster, 
            	'src_mp4' => $oTcvMp4->getFileUrl($iFile), 
            	'src_webm' => $oTcvWebm->getFileUrl($iFile)
            )
        );
    }
}

/** @} */
