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

bx_import('BxDolLanguages');

check_logged();

$oTemplate = BxDolTemplate::getInstance();

$oPage = BxDolPage::getObjectInstanceByURI();
if ($oPage) {

    $oTemplate->setPageNameIndex (BX_PAGE_DEFAULT);
    $oTemplate->setPageContent ('page_main_code', $oPage->getCode());
    $oTemplate->getPageCode();

} else {

    $oTemplate->displayPageNotFound();
}

/** @} */
