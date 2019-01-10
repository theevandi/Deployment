<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    Attendant Attendant
 * @ingroup     UnaModules
 *
 * @{
 */
define('BX_ATTENDANT_ON_PROFILE_CREATION_METHOD', 'browse_recommended');
define('BX_ATTENDANT_ON_PROFILE_CREATION_EVENT_AFTER_REGISTRATION', 'registration');
define('BX_ATTENDANT_ON_PROFILE_CREATION_EVENT_AFTER_CONFIRMATION', 'confirmation');

class BxAttendantModule extends BxDolModule
{
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
    }

    /**
     * Service methods
     */
    
    /**
     * @page service Service Calls
     * @section bx_attendant Attendant
     * @subsection bx_attendant-other Other
     * @subsubsection bx_attendant-on-profile get_profile_modules
     * 
     * @code bx_srv('bx_attendant', 'get_profile_modules', [...]); @endcode
     * 
     * Get list of avaliable modules for on_profile_creation event
     * 
     * @return an array with avaliable modules. 
     * 
     * @see BxAttendantModule::serviceGetProfileModules
     */
    /** 
     * @ref bx_attendant-get_profile_modules "get_profile_modules"
     */
    public function serviceGetProfileModules()
    {
        $aResult = array();
        $BxDolModuleQuery = BxDolModuleQuery::getInstance();
        $aModules = $BxDolModuleQuery->getModulesBy(array('type' => 'modules', 'active' => 1));
        foreach($aModules as $aModule){
            if(BxDolRequest::serviceExists($aModule['name'], BX_ATTENDANT_ON_PROFILE_CREATION_METHOD)){
                $aResult[$aModule['name']] = $aModule['title'];
            }
        }
        return $aResult;
    }
    
    
    /**
     * @page service Service Calls
     * @section bx_attendant Attendant
     * @subsection bx_attendant-other Other
     * @subsubsection bx_attendant-on-profile get_popup_with_recommended_on_event_show
     * 
     * @code bx_srv('bx_attendant', 'get_popup_with_recommended_on_event_show', [...]); @endcode
     * 
     * Get list of events for on_profile_creation show
     * 
     * @return an array with avaliable events. 
     * 
     * @see BxAttendantModule::serviceGetPopupWithRecommendedOnEventShow
     */
    /** 
     * @ref bx_attendant-get_popup_with_recommended_on_event_show "get_popup_with_recommended_on_event_show"
     */
    public function serviceGetPopupWithRecommendedOnEventShow()
    {
        $aResult = array();
        $aChoices = array(BX_ATTENDANT_ON_PROFILE_CREATION_EVENT_AFTER_CONFIRMATION, BX_ATTENDANT_ON_PROFILE_CREATION_EVENT_AFTER_REGISTRATION);
        foreach($aChoices as $sChoice)
            $aResult[$sChoice] = _t('_bx_attendant_popup_event_after_' . $sChoice);
        
        return $aResult;
    }
    
    /**
     * @page service Service Calls
     * @section bx_attendant Attendant
     * @subsection bx_attendant-other Other
     * @subsubsection bx_attendant-get_profile_modules handle_action_view
     * 
     * @code bx_srv('bx_attendant', 'handle_action_view', [...]); @endcode
     * 
     * Get Include for injection
     * 
     * @return an include code
     * 
     * @see BxAttendantModule::HandleActionView
     */
    /** 
     * @ref bx_attendant-handle_action_view "handle_action_view"
     */
    public function serviceHandleActionView()
    {
        if (!isLogged())
            return;
        $sRv = '';
        $aEvents = $this->_oDb->getEvents(array('type' => 'active_by_action_and_object_id', 'action' => 'view', 'object_id' => bx_get_logged_profile_id()));
        foreach($aEvents as $aEvent){
            $oRv = call_user_func_array(array($this, $aEvent['method']), array($aEvent['object_id']));
            if ($oRv !== false){
                $sRv .= $oRv;
                $this->_oDb->setEventProcessed($aEvent['id']);
            }
        }
        return $sRv;
    }
    
    public function serviceHandleActionNonView()
    {
       //for some other actions fex cron - not implemented
    }
    
    public function addEvent($sMethod, $sAction, $iObjectId)
    {
        $this->_oDb->addEvent($sMethod, $sAction, $iObjectId);
    }
    
    public function initPopupWithRecommendedOnProfileAdd($iProfileId)
    {
        $this->addEvent('processPopupWithRecommendedOnProfileAdd', 'view', $iProfileId);
    }
    
    public function processPopupWithRecommendedOnProfileAdd($iProfileId)
    {
        $sRv = '';
        $sEvent = getParam('bx_attendant_on_profile_event_list');
        $oProfile = BxDolProfile::getInstance($iProfileId);
        $oAccount = $oProfile ? $oProfile->getAccountObject() : null;
        if ($sEvent == BX_ATTENDANT_ON_PROFILE_CREATION_EVENT_AFTER_REGISTRATION || ($sEvent == BX_ATTENDANT_ON_PROFILE_CREATION_EVENT_AFTER_CONFIRMATION  && $oAccount != null &&  $oAccount->isConfirmed())){
            $aModules = explode(',', getParam('bx_attendant_on_profile_creation_modules'));
            $aModulteData = array();
            foreach($aModules as $sModuleName){
                if(BxDolRequest::serviceExists($sModuleName, BX_ATTENDANT_ON_PROFILE_CREATION_METHOD)){
                    $aTmp = BxDolService::call($sModuleName, BX_ATTENDANT_ON_PROFILE_CREATION_METHOD, array('unit_view' => 'showcase', 'empty_message' => false, "ajax_paginate" => false));
                    if (isset($aTmp['content'])){
                        $sTmp = $aTmp['content'];
                        $sTmp = str_replace('bx_conn_action', 'bx_attendant_conn_action', $sTmp);
                        $aModulteData[$sModuleName] = $sTmp;
                    }
                }
            }
            if (count($aModulteData) > 0)
                $sRv = $this->_oTemplate->popupWithRecommendedOnProfileAdd($aModulteData);
            return $sRv;
        }
        else{
            return false;
        }
    }
}

/** @} */
