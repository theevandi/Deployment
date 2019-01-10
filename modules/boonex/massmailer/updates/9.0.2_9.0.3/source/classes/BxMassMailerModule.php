<?php defined('BX_DOL') or die('hack attempt');
/**
* Copyright (c) UNA, Inc - https://una.io
* MIT License - https://opensource.org/licenses/MIT
*
* @defgroup    MassMailer Mass Mailer
* @ingroup     UnaModules
*
* @{
*/

class BxMassMailerModule extends BxBaseModGeneralModule
{
    private $aSegments = array();
          
    function __construct(&$aModule)
    {
        parent::__construct($aModule);
        $CNF = &$this->_oConfig->CNF;
        $aRv = array('lvl:0' => _t('_bx_massmailer_segments_all'));
        $oAccountQuery = BxDolAclQuery::getInstance();
              
        $aTmp = array();
        $oAccountQuery->getLevels(array('type' => 'all_active_pair'), $aTmp);
        foreach ($aTmp as $sKey => $sTmp){
            if ($sKey != MEMBERSHIP_ID_NON_MEMBER && $sKey != MEMBERSHIP_ID_ACCOUNT)
                $aRv['lvl:' . $sKey] = _t($sTmp);
        };
              
        $aTmp = $this->_oDb->getSegments();
        foreach ($aTmp as $sKey => $sTmp){
            $aRv['cst:' . $sKey] = _t($sTmp);
        };
        $this->aSegments = $aRv;
    }
          
    /**
    * ACTION METHODS
    */
    public function actionTrack($sActionName, $sHash)
    {
        if (isset($sHash) && trim($sHash) != ""){
            switch ($sActionName) {
                case 'click':
                    $sLink = $this->_oDb->updateDateClickForLink($sHash);
                    if (substr_count($sLink, '://') == 0)
                        $sLink = BX_DOL_URL_ROOT . $sLink;
                    header('Location: ' . $sLink);
                    break;
                    
                case 'seen':
                    header('Content-Type: image/png');
                    $this->_oDb->updateDateSeenForLetter($sHash);
                    break;
            }
        }
    }
    
    public function actionGetReportsData($sReportName, $sReportType, $sDateFrom, $sDateTo, $sSegment)
    {
        if(!BxDolAcl::getInstance()->isMemberLevelInSet(128))
            return '';
        
        header('Content-Type: application/json');
        
        $aColors = array('#3366CC', '#DC3912', '#FF9900', '#109618', '#990099');
        $iDateFrom = strtotime($sDateFrom);
        $iDateTo = strtotime($sDateTo);
        $sType = "bar";
        $bIsTimeX = false;
        $iMinValueY = 0;
        $iMaxValueY = 0;
        $aValues = array('labels' => array(), 'values' => array(array('legend' => '', 'data' => array())), 'links' => array(), 'strings' => array(0 => _t('_bx_massmailer_txt_item'), 1 => _t('_bx_massmailer_txt_value')));
        
        switch ($sReportName) {
            case "SUBSCRIBERS_INFO":
                $sType = "line";
                $bIsTimeX = true;
                $aValues['strings'][0] = _t('_bx_massmailer_txt_date');
                $aValues['strings'][1] = '';
                $sSql = $this->getSqlBySegment($sSegment);
                $aData = $this->_oDb->getAccountsByTermsStat($iDateFrom, $iDateTo, $sSql);
                $aData2 = $this->_oDb->getAccountsByTermsStatUnsubscribe($iDateFrom, $iDateTo, $sSql);
                $iValuePrev = $this->_oDb->getAccountsByTermsStatInitValue($iDateFrom, $sSql);
                $iValuePrev2 = $this->_oDb->getAccountsByTermsStatInitValueUnsubscribe($iDateFrom, $sSql);
                $aTmpDates = array();
                $aTmpDates2 = array();
                $aValues['values'][0]['legend'] = _t('_bx_massmailer_txt_subscribed');
                $aValues['values'][1] = array('legend' => _t('_bx_massmailer_txt_unsubscribed') , 'data' => array());
                foreach ($aData as $aValue) {
                    $aTmpDates[$aValue['period']] = $aValue['count'];
                }
                foreach ($aData2 as $aValue) {
                    $aTmpDates2[$aValue['period']] = $aValue['count'];
                }
                for ($i = $iDateFrom; $i < $iDateTo ; $i = $i + 86400 ){
                    $sX = date('Y-m-d', $i);
                    
                    if (!array_key_exists($sX, $aTmpDates)){
                        array_push($aValues['values'][0]['data'], array('x' => $sX, 'y' => $sReportType == 'content_total' ? $iValuePrev : 0));
                        
                    }
                    else{
                        array_push($aValues['values'][0]['data'], array('x' => $sX, 'y' => $sReportType == 'content_total' ? $iValuePrev : $aTmpDates[$sX]));
                        $iValuePrev += $aTmpDates[$sX];
                    }
                    
                    if (!array_key_exists($sX, $aTmpDates2)){
                        array_push($aValues['values'][1]['data'], array('x' => $sX, 'y' => $sReportType == 'content_total' ? $iValuePrev2 : 0));
                        
                    }
                    else{
                        array_push($aValues['values'][1]['data'], array('x' => $sX, 'y' => $sReportType == 'content_total' ? $iValuePrev2 : $aTmpDates2[$sX]));
                        $iValuePrev2 += $aTmpDates2[$sX];
                    }
                    
                    if ($sReportType == 'content_total'){
                        if ($iValuePrev > $iMaxValueY)
                            $iMaxValueY = $iValuePrev;
                        if ($iValuePrev2 > $iMaxValueY)
                            $iMaxValueY = $iValuePrev2;
                    }
                    else{
                        if (array_key_exists($sX, $aTmpDates) && $aTmpDates[$sX] > $iMaxValueY)
                            $iMaxValueY = $aTmpDates[$sX];
                        if (array_key_exists($sX, $aTmpDates2) && $aTmpDates2[$sX] > $iMaxValueY)
                            $iMaxValueY = $aTmpDates2[$sX];
                    }
                }
                break;
            
            case "CAMPAIGN_REPORT":
                $aDataStat = $this->_oDb->getStatByCampaignId($sReportType);
                $aValues['values'][0]['legend'] = _t('_bx_massmailer_txt_sent_total_title', $aDataStat['total']);
                array_push($aValues['values'][0]['data'], $aDataStat['total']);
                $aValues['values'][1] = array('legend' => _t('_bx_massmailer_txt_opened_total_title', $aDataStat['seen']) , 'data' => array($aDataStat['seen']));
                $aValues['values'][2] = array('legend' => _t('_bx_massmailer_txt_unopened_total_title', $aDataStat['total'] - $aDataStat['seen']), 'data' => array($aDataStat['total'] - $aDataStat['seen']));
                $aValues['values'][3] = array('legend' => _t('_bx_massmailer_txt_clicked_total_title', $aDataStat['clicked']), 'data' => array($aDataStat['clicked']));
                $aValues['values'][4] = array('legend' => _t('_bx_massmailer_txt_unsubscribed_total_title', $aDataStat['unsubscribed']), 'data' => array($aDataStat['unsubscribed']));
                $iMaxValueY = $aDataStat['total'];
                break;
        }
        
        $iMaxValueY = ceil($iMaxValueY * 1.1);
        
        $aDataForChartXAxes = array();
        if ($bIsTimeX){
            $sUnit = 'day';
            $iInterval = ($iDateTo - $iDateFrom) / 86400;
            if ($iInterval > 50)
                $sUnit = 'week';
            if ($iInterval > 100)
                $sUnit = 'month';
            
            $aDataForChartXAxes = array(
                'type' => 'time',
                'time' => array(
                    'tooltipFormat' => 'DD.MM.YYYY',
                    'unit' => $sUnit,
                 ),
                'display' => true,
                'distribution' => 'linear',
                'ticks' => array(
                    'display' => true,
                    'autoSkip' => true,
                )
            );
        }
        else{
            $aDataForChartXAxes = array(
                'ticks' => array(
                    'autoSkip' => false
                ),
                'display' => true
            );
        }
        
        $aDataForChartDatasets = array();
        for($i = 0; $i < count($aValues['values']); $i++){
            $aDataForChartDatasets[] = array(
                'label' => $aValues['values'][$i]['legend'],
                'fill' => false,
                'backgroundColor' => $aColors[$i],
                'borderColor' => $aColors[$i],
                'borderWidth' => 1,
                'data' => $aValues['values'][$i]['data']
            );
        }
        
        $aDataForChart = array(
            'type' => $sType,
            'data' => array(
                'labels' => $aValues['labels'],
                'datasets' => $aDataForChartDatasets
            ),
            'options' => array(
                'legend' => array(
                    'position' => 'bottom',
                    'display' => count($aValues['values']) == 1 ? false : true
                ),
                'scales' => array(
                    'yAxes' => array(
                        array(
                            'ticks' => array(
                                'max' => $iMaxValueY,
                                'min' => $iMinValueY,
                                'stepSize' => $this->getReportStep($iMinValueY, $iMaxValueY),
                                'autoSkip' => true
                            )
                        )
                    ),
                    'xAxes' => array($aDataForChartXAxes)
                )
            ),
            'links' => $aValues['links'],
            'strings' => $aValues['strings'],
        );
        
        echo json_encode($aDataForChart);
    }
  
    /**
    * SERVICE METHODS
    */
          
    /**
    * @page service Service Calls
    * @section bx_massmailer Mass mailer
    * @subsection bx_massmailer-page_blocks Page Blocks
     * @subsubsection bx_massmailer-campagn_subscribers campagn_subscribers
    * 
     * @code bx_srv('bx_massmailer', 'campagn_subscribers', [...]); @endcode
    * 
    * Get page block with the campagn's subscribers list
    *
    * @param $aParams an array with search params.
    * @return HTML string with block content to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
    * 
    * @see BxMassMailerModule::serviceCampagnSubscribers
    */
    /** 
    * @ref bx_massmailer-campagn_subscribers "entity_view"
    */
    public function serviceCampagnSubscribers ($iContentId = 0, $sDisplay = false)
    {
        if(!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);

        if(!$iContentId)
            return false;
              
        $aData = $this->_oDb->getLettersByCampaignId($iContentId);
        if (count($aData) > 0)
            return $this->_oTemplate->getSubscribers($aData);
        return $iContentId;
    }
    
    /**
     * @page service Service Calls
     * @section bx_massmailer Mass mailer
     * @subsection bx_massmailer-page_blocks Page Blocks
     * @subsubsection bx_massmailer-campagn_info campagn_info
     * 
     * @code bx_srv('bx_massmailer', 'campagn_info', [...]); @endcode
     * 
     * Get page block with the campagn info
     *
     * @param $aParams an array with search params.
     * @return HTML string with block content to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxMassMailerModule::serviceCampagnInfo
     */
    /** 
     * @ref bx_massmailer-campagn_info "campagn_info"
     */
    public function serviceCampagnInfo ($iContentId = 0, $sDisplay = false)
    {
        if(!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);

        if(!$iContentId)
            return false;
        
        return $this->_oTemplate->getInfo($iContentId);
    }
    
    /**
     * @page service Service Calls
     * @section bx_massmailer Mass mailer
     * @subsection bx_massmailer-page_blocks Page Blocks
     * @subsubsection bx_massmailer-campagn_links campagn_links
     * 
     * @code bx_srv('bx_massmailer', 'campagn_links', [...]); @endcode
     * 
     * Get page block with the campagn links list
     *
     * @param $aParams an array with search params.
     * @return HTML string with block content to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxMassMailerModule::serviceCampagnLinks
     */
    /** 
     * @ref bx_massmailer-campagn_links "campagn_links"
     */
    public function serviceCampagnLinks ($iContentId = 0, $sDisplay = false)
    {
        if(!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);

        if(!$iContentId)
            return false;
        
        $aData = $this->_oDb->getClicksByCampaignId($iContentId);
        if (count($aData) > 0)
            return $this->_oTemplate->getClicks($aData);
        return $iContentId;
    }
    
    /**
     * @page service Service Calls
     * @section bx_massmailer Mass mailer
     * @subsection bx_massmailer-page_blocks Page Blocks
     * @subsubsection bx_massmailer-total_subscribers total_subscribers
     * 
     * @code bx_srv('bx_massmailer', 'total_subscribers', [...]); @endcode
     * 
     * Get page block with subscribers charts
     *
     * @param $aParams an array with search params.
     * @return HTML string with block content to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxMassMailerModule::serviceTotalSubscribers
     */
    /** 
     * @ref bx_massmailer-total_subscribers "total_subscribers"
     */
    public function serviceTotalSubscribers ()
    {
        return $this->_oTemplate->getTotalSubscribers();
    }
    
    /**
     * @page service Service Calls
     * @section bx_massmailer Mass mailer
     * @subsection bx_massmailer-page_blocks Page Blocks
     * @subsubsection bx_massmailer-entity_breadcrumb entity_breadcrumb
     * 
     * @code bx_srv('bx_massmailer', 'entity_breadcrumb', [...]); @endcode
     * 
     * Get page block with page's breadcrumbs
     *
     * @param $aParams an array with search params.
     * @return HTML string with block content to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxMassMailerModule::serviceEntityBreadcrumb
     */
    /** 
     * @ref bx_massmailer-entity_breadcrumb "entity_breadcrumb"
     */
    public function serviceEntityBreadcrumb ($iContentId = 0)
    {
        $CNF = &$this->_oConfig->CNF;
        $oPermalink = BxDolPermalinks::getInstance();
        if (!$iContentId)
            $iContentId = bx_process_input(bx_get('id'), BX_DATA_INT);
        if (!$iContentId){
            $aContentInfo[$CNF['FIELD_TITLE']] = _t('_bx_massmailer_txt_breadcrumb_title_new_campaign');
            $aContentInfo['url'] = $oPermalink->permalink('page.php?i=' . $CNF['URI_ADD_CAMPAIGN']);
        }
        else{
            $aContentInfo = $this->_oDb->getCampaignInfoById($iContentId);
            if (!$aContentInfo)
                return false;
            $aContentInfo['url'] = $oPermalink->permalink('page.php?i=' . $CNF['URI_VIEW_CAMPAIGN'] . '&id=' . $aContentInfo[$CNF['FIELD_ID']]);
        }
        return $this->_oTemplate->entryBreadcrumb($aContentInfo);
    }
    
    /**
     * @page service Service Calls
     * @section bx_massmailer Mass mailer
     * @subsection bx_massmailer-page_blocks Page Blocks
     * @subsubsection bx_massmailer-attributes attributes
     * 
     * @code bx_srv('bx_massmailer', 'attributes', [...]); @endcode
     * 
     * Get page block with attributes list
     *
     * @param $aParams an array with search params.
     * @return HTML string with block content to display on the site. All necessary CSS and JS files are automatically added to the HEAD section of the site HTML.
     * 
     * @see BxMassMailerModule::serviceAttributes
     */
    /** 
     * @ref bx_massmailer-attributes "attributes"
     */
    public function serviceAttributes ()
    {
        $aAttributesParts = array();
        $aAttributesParts['account'] = array(_t('_bx_massmailer_txt_attribute_global'), array('email' => _t('_bx_massmailer_txt_attribute_email'), 'account_name' => _t('_bx_massmailer_txt_attribute_account_name'), 'account_id' => _t('_bx_massmailer_txt_attribute_account_id'), 'unsubscribe_url' => _t('_bx_massmailer_txt_attribute_unsubscribe_url')));
        $BxDolModuleQuery = BxDolModuleQuery::getInstance();
        $aModules = $BxDolModuleQuery->getModulesBy(array('type' => 'modules', 'active' => 1));
        foreach($aModules as $aModule){
            if(BxDolRequest::serviceExists($aModule['name'], 'act_as_profile') && BxDolService::call($aModule['name'], 'act_as_profile') == true){
                $oModule = BxDolModule::getInstance($aModule['name']);
                $aTmp2 = array();
                $aTmp = $oModule->serviceGetSearchableFieldsExtended('view');
                foreach($aTmp as $sKey => $aField){
                    $aTmp2[$sKey] = _t($aField['caption']);
                }
                $aAttributesParts[$aModule['name']] = array($aModule['title'], $aTmp2);
            }
        }
        return $this->_oTemplate->getAttributes($aAttributesParts);
    }
    
    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedAdd ($isPerformAction = false)
    {
        return $this->checkAllowed($isPerformAction);
    }
    
    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedEdit ($aDataEntry, $isPerformAction = false)
    {
        return $this->checkAllowed($isPerformAction);
    }

    /**
     * @return CHECK_ACTION_RESULT_ALLOWED if access is granted or error message if access is forbidden. So make sure to make strict(===) checking.
     */
    public function checkAllowedDelete (&$aDataEntry, $isPerformAction = false)
    {
        return $this->checkAllowed($isPerformAction);
    }
          
    public function getSegments($sKey = "")
    {
        if ($sKey == ""){
            return $this->aSegments;
        }
        else{
            if (isset($this->aSegments[$sKey]))
                return $this->aSegments[$sKey];
            return false;
        }
    }
          
    public function sendTest($sEmail, $iCampaignId)
    {
        $aTmp = $this->getDataForCampaign($iCampaignId);
        $aTemplate = $aTmp[0];
        $aCustomHeaders = $aTmp[1];
              
        if (!$aTemplate)
            return false;
        
        return $this->sendLetter($sEmail, $iCampaignId, $aCustomHeaders, bx_get_logged_profile_id(), $aTemplate, false);
    }
          
    public function sendAll($iCampaignId)
    {
        $aEmails = array();
        $aTmp = $this->getDataForCampaign($iCampaignId);
        $aTemplate = $aTmp[0];
        $aCustomHeaders = $aTmp[1];
        $aCampaign = $aTmp[2];
              
        if (!$aTemplate)
            return false;
        $this->_oDb->deleteCampaignData($iCampaignId);
        $aAccounts = $this->getEmailsBySegment($aCampaign['segments'], $aCampaign['is_one_per_account']);
        foreach ($aAccounts as $aAccountInfo){
            array_push($aEmails, $aAccountInfo['email']);
            $this->sendLetter($aAccountInfo['email'], $iCampaignId, $aCustomHeaders, $aAccountInfo['profile_id'], $aTemplate, true);
        }
              
        $this->_oDb->sendCampaign($iCampaignId, implode(',', $aEmails));
        return true;
    }
    
    private function sendLetter($sEmail, $iCampaignId, $aCustomHeaders, $iProfileId, $aTemplate, $bAddToQueue)
    {
        $sLetterCode = $this->_oDb->addLetter($iCampaignId, $sEmail);
       
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        $aTemplate['Body'] = preg_replace_callback("/$regexp/siU",
            function ($aMatch) use ($iCampaignId, $sLetterCode) {
                if ($aMatch[2] != '{unsubscribe_url}'){
                    $sLinkCode = $this->_oDb->addLink($sLetterCode, $aMatch[2], $aMatch[3], $iCampaignId);
                    return str_replace($aMatch[2], BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'track/click/' . $sLinkCode . "/", $aMatch[0]);
                }
                else{
                    return $aMatch[0];
                }
            },
            $aTemplate['Body']);
        return sendMail($sEmail, $aTemplate['Subject'], $aTemplate['Body'], 0, $this->addMarkers($iProfileId, $sLetterCode), BX_EMAIL_MASS, 'html', false, $aCustomHeaders, $bAddToQueue);
    }

    public function getEmailCountInSegment($iCampaignId)
    {
        $aTmp = $this->getDataForCampaign($iCampaignId);
        $aCampaign = $aTmp[2];
        $aAccounts = $this->getEmailsBySegment($aCampaign['segments'], $aCampaign['is_one_per_account']);
        return count($aAccounts);
    }
          
    private function getEmailsBySegment($sSegment, $bIsOnePerAccount)
    {
        $sTerms = $this->getSqlBySegment($sSegment);
        if ($bIsOnePerAccount){
            $sTerms .= "AND `tp`.`id` = `ta`.`profile_id` ";
        }
        return $this->_oDb->getAccountsByTerms($sTerms);
    }
    
    private function checkAllowed($isPerformAction = false)
    {
        $aCheck = checkActionModule($this->_iProfileId, 'use massmailer', $this->getName(), $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
             return _t('_sys_txt_access_denied');
        return CHECK_ACTION_RESULT_ALLOWED;
    }
    
    private function getSqlBySegment($sSegment)
    {
        $sRv = '';
        if (substr_count($sSegment, 'lvl:') > 0){
            $sLvl = str_replace('lvl:', '', $sSegment);
            switch ($sLvl) {
                case 0:
                    $sRv = '';
                    break;
                case MEMBERSHIP_ID_UNCONFIRMED:
                    $sRv = " AND `ta`.`email_confirmed` = 0 AND `ta`.`phone_confirmed` = 0 ";
                    break;
                case MEMBERSHIP_ID_SUSPENDED:
                    $sRv = " AND `tp`.`status` = 'suspended' ";
                    break;
                case MEMBERSHIP_ID_PENDING:
                    $sRv = " AND `tp`.`status` = 'pending' ";
                    break;
                case MEMBERSHIP_ID_STANDARD:
                    $sRv = " AND `tp`.`id` NOT IN (SELECT `IDMember` FROM `sys_acl_levels_members`) ";
                    break;
                default:
                    $sRv = $this->_oDb->prepareAsString(" AND `tp`.`id` IN (SELECT `IDMember` FROM `sys_acl_levels_members` WHERE IDLevel = ?) ", $sLvl);
                    break;
            }
            $sRv .= " AND `tp`.`type` IN ('" . implode("','", $this->getProfileModules()) . "')"; 
        }
        return $sRv;
    }
    
    private function getProfileModules()
    {
        $aResult = array();
        $BxDolModuleQuery = BxDolModuleQuery::getInstance();
        $aModules = $BxDolModuleQuery->getModulesBy(array('type' => 'modules', 'active' => 1));
        foreach($aModules as $aModule){
            if(BxDolRequest::serviceExists($aModule['name'], 'act_as_profile') && BxDolService::call($aModule['name'], 'act_as_profile') == true){
                array_push($aResult, $aModule['name']);
            }
        }
        return $aResult;
    }
          
    private function getDataForCampaign($iCampaignId)
    {
        $aCampaign = $this->_oDb->getCampaignInfoById($iCampaignId);
        $oEmailTemplates = BxDolEmailTemplates::getInstance();
        $aTemplate = $oEmailTemplates->parseTemplate('bx_massmailer_email', array('content' => $aCampaign['body']));
        $aTemplate['Subject'] = $aCampaign['subject'];
              
        $aCustomHeaders = array();
        $sFrom = $aCampaign['from_name'] != '' ? $aCampaign['from_name'] : getParam('bx_massmailer_initial_from_email');
        $aCustomHeaders['From'] = "=?UTF-8?B?" . base64_encode($sFrom) . "?= <" . getParam('site_email_notify') . ">" ;
        if ($aCampaign['reply_to'] != ''){
            $sFrom = $aCampaign['from_name'] != '' ? $aCampaign['from_name'] : getParam('site_title');
            $aCustomHeaders['Reply-To'] = "=?UTF-8?B?" . base64_encode($sFrom) . '?= <' . bx_process_output($aCampaign['reply_to']) . '>';
            $aCustomHeaders['X-Original-From'] = "=?UTF-8?B?" . base64_encode($sFrom) . '?= <' . bx_process_output($aCampaign['reply_to']) . '>';
        }
              
        return array($aTemplate, $aCustomHeaders, $aCampaign);
    }
          
    private function addMarkers($iProfileId, $sLetterCode)
    {
        $CNF = &$this->_oConfig->CNF;
        $aMarkers = array();
        $oProfile = BxDolProfile::getInstance($iProfileId);
        if ($oProfile){
            $oAccount = BxDolAccount::getInstance($oProfile->getAccountId());
            $aMarkers['email'] = $oAccount->getEmail();
            $aMarkers['account_name'] = $oAccount->getDisplayName();
            $aMarkers['account_id'] = $oProfile->getAccountId();
            $oModule = BxDolModule::getInstance($oProfile->getModule());
            if (BxDolRequest::serviceExists($oProfile->getModule(), 'get_info')){
                $aProfileInfo = $oModule->serviceGetInfo($oProfile->getContentId(), false);
                foreach ($aProfileInfo as $sKey => $sValue){
                    if (!isset($aMarkers[$sKey]))
                    $aMarkers[$sKey] = $sValue;
                }
            
                $aMarkers['seen_image_url'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'track/seen/' . $sLetterCode . "/";
                $aMarkers['unsubscribe_url'] = BX_DOL_URL_ROOT . $oAccount->getUnsubscribeLink(BX_EMAIL_MASS) . "&lhash=" . $sLetterCode;
            }
            
        }
        return $aMarkers;
    }
    
    private function getReportStep($iMin, $iMax)
    {
        $iCount = 10;
        return ceil(($iMax - $iMin) / $iCount);
    }
}

/** @} */