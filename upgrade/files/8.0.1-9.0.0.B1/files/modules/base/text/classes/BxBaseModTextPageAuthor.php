<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    BaseText Base classes for text modules
 * @ingroup     TridentModules
 *
 * @{
 */

/**
 * Profile's entries page.
 */
class BxBaseModTextPageAuthor extends BxTemplPage
{
    protected $_aProfileInfo;
    protected $_oProfile;

    public function __construct($aObject, $oTemplate = false)
    {
        parent::__construct($aObject, $oTemplate);

        // get profile info
        $iProfileId = bx_process_input(bx_get('profile_id'), BX_DATA_INT);
        if ($iProfileId) {
            $this->_oProfile = BxDolProfile::getInstance($iProfileId);
            $this->_aProfileInfo = $this->_oProfile ? $this->_oProfile->getInfo() : false;
        }

        if (!$this->_aProfileInfo || !$this->_oProfile)
            return;

        // select view profile submenu
        $oMenuSubmenu = BxDolMenu::getObjectInstance('sys_site_submenu');
        if ($oMenuSubmenu) {
            $sProfileObject = BxDolService::call($this->_oProfile->getModule(), 'get_submenu_object');
            if ($sProfileObject)
                $oMenuSubmenu->setObjectSubmenu($sProfileObject, array (
                    'title' => $this->_oProfile->getDisplayName(),
                    'link' => $this->_oProfile->getUrl(),
                    'icon' => $this->_oProfile->getIconModule(),
                ));
        }

        // add replaceable markers
        $this->addMarkers($this->_aProfileInfo); // every profile field can be used as marker
        $this->addMarkers(array('profile_id' => $this->_oProfile->id())); // profile id 
        $this->addMarkers(array('display_name' => $this->_oProfile->getDisplayName())); // profile display name 
        $this->addMarkers(array('profile_link' => $this->_oProfile->getUrl())); // profile link
    }

}

/** @} */
