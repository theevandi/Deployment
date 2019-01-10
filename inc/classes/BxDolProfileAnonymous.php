<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

/**
 * When profile is anonymous then use this special class.
 *
 * @section example Example of usage
 *
 * @code
 * if (0 > $iId)
 *     $oProfile = BxDolProfileAnonymous::getInstance();
 * else
 *     $oProfile = BxDolProfile::getInstance($iId);
 *
 * @endcode
 */
class BxDolProfileAnonymous extends BxDolProfileUndefined
{
    protected $_oProfileOrig = null;
    protected $_iProfileID = 0;
    protected $_isShowRealProfile = null;

    /**
     * Constructor
     */
    protected function __construct ($oProfile)
    {
        $sClass = get_class($this) . '_' . $oProfile->id();
        if (isset($GLOBALS['bxDolClasses'][$sClass]))
            trigger_error ('Multiple instances are not allowed for the class: ' . get_class($this), E_USER_ERROR);

        parent::__construct();

        $this->_iProfileID = $oProfile->id();
        $this->_oProfileOrig = $oProfile;
    }

    /**
     * Get singleton instance of Profile by profile id
     */
    public static function getInstance($mixedProfileId = false, $bClearCache = false)
    {
        $oProfile = BxDolProfile::getInstance(abs($mixedProfileId));
        if (!$oProfile)
            return BxDolProfileUndefined::getInstance();

        $sClass = __CLASS__ . '_' . $oProfile->id();
        if (!isset($GLOBALS['bxDolClasses'][$sClass]))
            $GLOBALS['bxDolClasses'][$sClass] = new BxDolProfileAnonymous($oProfile);

        return $GLOBALS['bxDolClasses'][$sClass];
    }

    /**
     * Get profile display name
     */
    public function getDisplayName()
    {
        if ($this->isShowRealProfile())
            return _t('_anonymous_f', $this->_oProfileOrig->getDisplayName());
        else
            return _t('_anonymous');
    }

    public function getUrl()
    {
        if ($this->isShowRealProfile())
            return $this->_oProfileOrig->getUrl();
        else
            return 'javascript:void(0);';
    }
    
    protected function isShowRealProfile() 
    {
        if (null !== $this->_isShowRealProfile)
            return $this->_isShowRealProfile;

        $this->_isShowRealProfile = (isAdmin() || $this->_oProfileOrig->id() == bx_get_logged_profile_id() || BxDolAcl::getInstance()->isMemberLevelInSet(array(MEMBERSHIP_ID_ADMINISTRATOR, MEMBERSHIP_ID_MODERATOR)));

        return $this->_isShowRealProfile;
    }
}

/** @} */
