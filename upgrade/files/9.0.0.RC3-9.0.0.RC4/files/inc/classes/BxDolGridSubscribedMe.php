<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaCore UNA Core
 * @{
 */

require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

class BxDolGridSubscribedMe extends BxTemplGrid
{
    protected $_bInit = false;

    protected $_bOwner = false;
    protected $_iProfileId = 0;

    protected $_oConnection;
    protected $_sConnectionObject = 'sys_profiles_subscriptions';
    protected $_sConnectionMethod = 'getConnectedInitiatorsAsSQLParts';  

    public function __construct ($aOptions, $oTemplate = false)
    {
        parent::__construct ($aOptions, $oTemplate);
        $this->_sDefaultSortingOrder = 'DESC';

        if(($iProfileId = bx_get('profile_id')) !== false)
            $this->setProfileId($iProfileId);   
    }

    public function init()
    {
        if(!$this->_iProfileId)
            return false;

        $oProfile = BxDolProfile::getInstance($this->_iProfileId);
        if(!$oProfile)
            return false;

        if($oProfile->id() == bx_get_logged_profile_id())
            $this->_bOwner = true;

        $this->_oConnection = BxDolConnection::getObjectInstance($this->_sConnectionObject);
        if(!$this->_oConnection)
            return false;

        $aSQLParts = $this->_oConnection->{$this->_sConnectionMethod}('p', 'id', $oProfile->id());

        $this->addMarkers(array(
            'profile_id' => $oProfile->id(),
            'join_connections' => $aSQLParts['join']
        ));

        return true;
    }

    public function setProfileId($iProfileId)
    {
        $this->_iProfileId = (int)$iProfileId;

        $this->_bInit = $this->init();
    }

    public function getCode ($isDisplayHeader = true)
    {
        if(!$this->_bInit)
            return '';

        return parent::getCode($isDisplayHeader);        
    }

    /**
     * 'accept' action handler
     */
    public function performActionSubscribe()
    {
        list($iId, $iViewedId) = $this->_prepareIds();
        if(!$iId)
            return echoJson(array('msg' => _t('_sys_txt_error_occured')));

        $a = $this->_oConnection->actionAdd($iId, $iViewedId);
        if(!empty($a['err']))
            return echoJson(array('msg' => $a['msg']));

        return echoJson(array('grid' => $this->getCode(false), 'blink' => $iId));
    }

    protected function _getCellName ($mixedValue, $sKey, $aField, $aRow)
    {
        $oProfile = BxDolProfile::getInstance($aRow['id']);
        if (!$oProfile)
            return _t('_sys_txt_error_occured');

        return parent::_getCellDefault ($oProfile->getUnit(), $sKey, $aField, $aRow);
    }

    protected function _getActionSubscribe ($sType, $sKey, $a, $isSmall = false, $isDisabled = false, $aRow = array())
    {
        $iViewerId = bx_get_logged_profile_id();
        if(!isLogged() || $iViewerId == $aRow['id'] || $this->_oConnection->isConnected($iViewerId, $aRow['id']))
            return '';

        return parent::_getActionDefault ($sType, $sKey, $a, $isSmall, $isDisabled, $aRow);
    }

    protected function _prepareIds ()
    {
        $iViewedId = bx_get_logged_profile_id();

        $iId = 0;
        $aIds = bx_get('ids');
        if($aIds && is_array($aIds))
            $mixedId = array_pop($aIds);

        if(strpos($mixedId, ':') !== false) {
            list($iId, $iViewedId) = explode (':', $mixedId);

            $iId = (int)$iId;
            $iViewedId = (int)$iViewedId;
        }
        else 
            $iId = (int)$mixedId;

        return array($iId, $iViewedId);
    }
}

/** @} */
