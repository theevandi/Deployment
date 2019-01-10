<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Antispam Antispam
 * @ingroup     TridentModules
 *
 * @{
 */

/**
 * Spam detection based on the message content and logged in user info - http://akismet.com
 */
class BxAntispamAkismet extends BxDol
{
    protected $oAkismet = null;

    public function __construct($iAccoutId = 0)
    {
        parent::__construct();
        $sKey = getParam('bx_antispam_akismet_api_key');
        if ($sKey && $oAccount = BxDolAccount::getInstance((int)$iAccoutId)) {
            require_once (BX_DIRECTORY_PATH_PLUGINS . 'akismet/Akismet.class.php');
            $this->oAkismet = new Akismet(BX_DOL_URL_ROOT, $sKey);

            $oProfile = BxDolProfile::getInstanceByAccount((int)$iAccoutId);

            $this->oAkismet->setCommentAuthorEmail($oAccount->getEmail());
            $this->oAkismet->setCommentAuthor($oProfile->getDisplayName());
            $this->oAkismet->setCommentAuthorURL($oProfile->getUrl());
        }
    }

    public function isSpam ($s, $sPermalink = false)
    {
        if (!$this->oAkismet)
            return false;

        $this->oAkismet->setCommentContent($s);
        if ($sPermalink)
            $this->oAkismet->setPermalink($sPermalink);

        return $this->oAkismet->isCommentSpam();
    }

    public function onPositiveDetection ($sExtraData = '')
    {
        $o = bx_instance('DNSBlacklists', array(), 'bx_antispam');
        $o->onPositiveDetection (getVisitorIP(), $sExtraData, 'akismet');
    }
}

/** @} */
