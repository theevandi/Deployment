<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Albums Albums
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Create/Edit entry form
 */
class BxAlbumsFormEntry extends BxBaseModTextFormEntry
{
    public function __construct($aInfo, $oTemplate = false)
    {
        $this->MODULE = 'bx_albums';
        parent::__construct($aInfo, $oTemplate);
    }

    public function processFiles ($sFieldFile, $iContentId = 0, $isAssociateWithContent = false)
    {
        if ($isAssociateWithContent)
            return parent::processFiles ($sFieldFile, $iContentId, $isAssociateWithContent);

        $aMediasOld = $this->_oModule->_oDb->getMediaListByContentId($iContentId);                
        
        if ($b = parent::processFiles ($sFieldFile, $iContentId, $isAssociateWithContent)) {
            $aMediasNew = $this->_oModule->_oDb->getMediaListByContentId($iContentId);
            $aIdsOld = array_column($aMediasOld, 'id');
            $aIdsNew = array_column($aMediasNew, 'id');
            $aIdsAdded = array_diff($aIdsNew, $aIdsOld);
            
            $iProfileId = $this->getContentOwnerProfileId($iContentId);
            if (!empty($aIdsAdded))
                bx_alert($this->_oModule->getName(), 'medias_added', $iContentId, $iProfileId, array(
                    'object_author_id' => $iProfileId,

                    'subobjects_ids' => $aIdsAdded,

                    'medias_added' => $aIdsAdded,
                ));
        }

        return $b;
    }
    
    protected function _associalFileWithContent($oStorage, $iFileId, $iProfileId, $iContentId, $sPictureField = '')
    {
        parent::_associalFileWithContent($oStorage, $iFileId, $iProfileId, $iContentId, $sPictureField);

        $CNF = &$this->_oModule->_oConfig->CNF;

        $oTranscoder = BxDolTranscoderImage::getObjectInstance($CNF['OBJECT_IMAGES_TRANSCODER_BIG']);
        
        $sData = '';
        $sExif = '';
        $aExif = false;
        $aFile = $oStorage->getFile($iFileId);
        if ($oTranscoder->isMimeTypeSupported($aFile['mime_type'])) {
            $oImageReize = BxDolImageResize::getInstance();

            $a = $oImageReize->getImageSize($oTranscoder->getFileUrl($iFileId));
            $sData = isset($a['w']) && isset($a['h']) ? $a['w'] . 'x' . $a['h'] : '';

            if ($aExif = $oImageReize->getExifInfo($oStorage->getFileUrlById($iFileId))) {
                $a = array('Make', 'Model', 'FocalLength', 'ShutterSpeedValue', 'ExposureTime', 'ISOSpeedRatings', 'Orientation', 'Artist', 'Copyright', 'Flash', 'WhiteBalance', 'DateTimeOriginal', 'DateTimeDigitized', 'ExifVersion', 'COMPUTED', 'GPSLatitudeRef', 'GPSLatitude', 'GPSLongitudeRef', 'GPSLongitude', 'GPSAltitudeRef', 'GPSAltitude', 'GPSTimeStamp', 'GPSImgDirectionRef', 'GPSImgDirection', 'GPSDateStamp');
                $aExifFiltered = array();
                foreach ($a as $sIndex)
                    if (isset($aExif[$sIndex]))
                        $aExifFiltered[$sIndex] = $aExif[$sIndex];
                $sExif = serialize($aExifFiltered);
            }
        }
        
        if (false === $this->_oModule->_oDb->associateFileWithContent ($iContentId, $iFileId, $iProfileId, $this->getCleanValue('title-' . $iFileId), $sData, $sExif))
            return;

        $aMediaInfo = $this->_oModule->_oDb->getMediaInfoSimpleByFileId($iFileId);
        $aContentInfo = $this->_oModule->_oDb->getContentInfoById($iContentId);

        if ($aMediaInfo && !empty($CNF['OBJECT_METATAGS_MEDIA'])) {            
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS_MEDIA']);
            if ($oMetatags->keywordsIsEnabled())
                $oMetatags->keywordsAdd($aMediaInfo['id'], $aMediaInfo['title']);
        }

        if ($aMediaInfo && isset($aExif['Make']) && !empty($CNF['OBJECT_METATAGS_MEDIA_CAMERA'])) {
            $oMetatags = BxDolMetatags::getObjectInstance($CNF['OBJECT_METATAGS_MEDIA_CAMERA']);
            if ($oMetatags->keywordsIsEnabled())
                $oMetatags->keywordsAddOne($aMediaInfo['id'], $oMetatags->keywordsCameraModel($aExif));
        }

        bx_alert($this->_oModule->getName(), 'media_added', $iContentId, $iProfileId, array(
            'object_author_id' => $aContentInfo[$CNF['FIELD_AUTHOR']],

            'subobject_id' => $aMediaInfo['id'],

            'media_id' => $aMediaInfo['id'], 
            'media_info' => $aMediaInfo,
        ));

        bx_alert($this->_oModule->getName() . '_media', 'added', $aMediaInfo['id'], $iProfileId, array(
            'object_id' => $iContentId, 
            'object_author_id' => $aContentInfo[$CNF['FIELD_AUTHOR']], 

            'media_info' => $aMediaInfo
        ));
    }

    function delete ($iContentId, $aContentInfo = array())
    {
        if (!($bRet = parent::delete ($iContentId, $aContentInfo)))
            return $bRet;

        if (!($aMediaList = $this->_oModule->_oDb->getMediaListByContentId($iContentId)))
            return $bRet;

        foreach ($aMediaList as $aMediaInfo)
            $this->_oModule->serviceDeleteFileAssociations($aMediaInfo['file_id']);
        
        return $bRet;
    }
}

/** @} */
