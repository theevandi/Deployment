<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentCore Trident Core
 * @{
 */

require_once('./inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

bx_import('BxTemplSearch');
bx_import('BxDolTemplate');

$oSearch = new BxTemplSearch(bx_get('section'));
$oSearch->setLiveSearch(bx_get('live_search') ? 1 : 0);
$oSearch->setMetaType(bx_process_input(bx_get('type')));

$sCode = '';
if (bx_get('keyword')) {
    $sCode = $oSearch->response();
    if (!$sCode)
        $sCode = $oSearch->getEmptyResult();
}

$oTemplate = BxDolTemplate::getInstance();
$oTemplate->setPageNameIndex (BX_PAGE_DEFAULT);
$oTemplate->setPageHeader (_t("_Search"));
$oTemplate->setPageContent ('page_main_code', $oSearch->getForm() . $oSearch->getResultsContainer($sCode));
$oTemplate->getPageCode();

/** @} */
