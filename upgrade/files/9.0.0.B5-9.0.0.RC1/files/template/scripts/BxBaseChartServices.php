<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    UnaBaseView UNA Base Representation Classes
 * @{
 */

/**
 * Services for chart objects functionality
 * @see BxDolChart
 */
class BxBaseChartServices extends BxDol
{
    public function serviceCheckAllowedView($isPerformAction = false)
    {
        $iProfileId = bx_get_logged_profile_id();

        $aCheck = checkActionModule($iProfileId, 'chart view', 'system', $isPerformAction);
        if($aCheck[CHECK_ACTION_RESULT] !== CHECK_ACTION_RESULT_ALLOWED)
            return $aCheck[CHECK_ACTION_MESSAGE];

        return CHECK_ACTION_RESULT_ALLOWED;
    }
    
    public function serviceGetChartGrowth()
    {
        $mixedResult = BxDolService::call('system', 'check_allowed_view', array(), 'TemplChartServices');
        if($mixedResult !== CHECK_ACTION_RESULT_ALLOWED)
            return '';

        $sDateFrom = date('Y-m-d', time() - 30*24*60*60);
        $sDateTo = date('Y-m-d', time());

        $aForm = array(
            'form_attrs' => array(
                'id' => 'bx_chart_controls',
                'action' => ''
            ),
            'inputs' => array (
                'object' => array(
                    'type' => 'select',
                    'name' => 'object',
                    'caption' => _t('_sys_chart_growth_object'),
                    'info' => '',
                    'value' => '',
                    'values' => array(),
                    'required' => '0',
                    'attrs' => array(
                        'id' => 'bx_chart_growth_objects',
                        'onchange' => 'oBxDolChartGrowth.loadData()'
                    ),
                ),
                'date_from' => array(
                    'type' => 'datepicker',
                    'name' => 'date_from',
                    'caption' => _t('_sys_chart_growth_date_from'),
                    'info' => '',
                    'value' => $sDateFrom,
                    'values' => array(),
                    'required' => '0',
                    'attrs' => array(
                		'id' => 'bx_chart_growth_date_from',
                        'onchange' => 'oBxDolChartGrowth.loadData()'
                    ),
                ),
                'date_to' => array(
                    'type' => 'datepicker',
                    'name' => 'date_to',
                    'caption' => _t('_sys_chart_growth_date_to'),
                    'info' => '',
                    'value' => $sDateTo,
                    'values' => array(),
                    'required' => '0',
                    'attrs' => array(
                		'id' => 'bx_chart_growth_date_to',
                        'onchange' => 'oBxDolChartGrowth.loadData()'
                    ),
                )
            )
        );

        $aObjects = BxDolChartQuery::getChartObjects();
        foreach($aObjects as $aObject)
            $aForm['inputs']['object']['values'][] = array('key' => $aObject['object'], 'value' => _t($aObject['title']));

        $oForm = new BxTemplFormView($aForm);

        $oTemplate = BxDolTemplate::getInstance();
        $oTemplate->addJs(array('chart.min.js', 'BxDolChartGrowth.js'));
        $oTemplate->addCss(array('chart.css'));

        return $oTemplate->parseHtmlByName('chart_growth.html', array(
            'date_from' => $sDateFrom,
            'date_to' => $sDateTo,
            'controls' => $oForm->getCode()
        ));
    }

    public function serviceGetChartStats()
    {
        $mixedResult = BxDolService::call('system', 'check_allowed_view', array(), 'TemplChartServices');
        if($mixedResult !== CHECK_ACTION_RESULT_ALLOWED)
            return '';

        $aTmplVarsItems = array();
        $aTmplVarsDataLabels = $aTmplVarsDataSet = array();

        $oDb = BxDolDb::getInstance();

        $sQuery = "SELECT * FROM `sys_statistics` WHERE 1 ORDER BY `order`";
        $aItems = $oDb->getAll($sQuery);

        foreach($aItems as $aItem) {
            $sTitle = _t($aItem['title']);
            $sTitleAttr = bx_html_attribute(_t($aItem['title']));

            $iValue = 0;
            if(!empty($aItem['query']))
                $iValue = (int)$oDb->getOne($aItem['query']);
            else if(BxDolRequest::serviceExists($aItem['module'], 'get_query_statistics'))
                $iValue = (int)BxDolService::call($aItem['module'], 'get_query_statistics', array($aItem));

            $sLink = '';
            if(!empty($aItem['link']))
                $sLink = BxDolPermalinks::getInstance()->permalink($aItem['link']);
            $bLink = !empty($sLink);

            $aTmplVarsItems[] = array(
            	'title' => $sTitle,
            	'title_attr' => $sTitleAttr,
                'value' => $iValue,
                'bx_if:show_link' => array(
                    'condition' => $bLink,
                    'content' => array(
                        'link' => $sLink,
                        'title' => $sTitle,
            			'title_attr' => $sTitleAttr,
                		'value' => $iValue,
                    )
                ),
                'bx_if:show_text' => array(
                    'condition' => !$bLink,
                    'content' => array(
                        'title' => $sTitle,
                		'title_attr' => $sTitleAttr,
                		'value' => $iValue,
                    )
                )
            );

            $aTmplVarsDataLabels[] = $sTitle;

            $aTmplVarsDataSet['data'][] = $iValue;
            $aTmplVarsDataSet['backgroundColor'][] = '#' . dechex(rand(0x000000, 0xFFFFFF));
        }

        $oTemplate = BxDolTemplate::getInstance();
        $oTemplate->addJs(array('chart.min.js'));
        $oTemplate->addCss(array('chart.css'));

        return $oTemplate->parseHtmlByName('chart_stats.html', array(
        	'bx_repeat:items' => $aTmplVarsItems,
            'chart_data' => json_encode(array(
                'labels' => $aTmplVarsDataLabels,
                'datasets' => array($aTmplVarsDataSet)
            ))
        ));
    }
}

/** @} */
