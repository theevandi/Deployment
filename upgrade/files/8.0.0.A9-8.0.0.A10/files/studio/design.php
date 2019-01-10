<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    TridentEndAdmin Trident Studio End Admin Pages
 * @ingroup     TridentStudio
 * @{
 */

require_once('./../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');

bx_import('BxDolLanguages');

bx_require_authentication(true);

$sName = bx_get('name');
if($sName === false)
    $sName = bx_get('templ_value');
$sName = $sName !== false ? bx_process_input($sName) : '';

$sPage = bx_get('page');
$sPage = $sPage !== false ? bx_process_input($sPage) : '';

$sCustomTemplateClass = BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $sName . '/scripts/BxTemplDesign.php';
if(file_exists($sCustomTemplateClass)) {
    require_once($sCustomTemplateClass);
    $oPage = new BxTemplDesign($sName, $sPage);
} else {
    bx_import('BxTemplStudioDesign');
    $oPage = new BxTemplStudioDesign($sName, $sPage);
}

bx_import('BxDolStudioTemplate');
$oTemplate = BxDolStudioTemplate::getInstance();
$oTemplate->setPageNameIndex($oPage->getPageIndex());
$oTemplate->setPageHeader($oPage->getPageHeader());
$oTemplate->setPageContent('page_caption_code', $oPage->getPageCaption());
$oTemplate->setPageContent('page_attributes', $oPage->getPageAttributes());
$oTemplate->setPageContent('page_menu_code', $oPage->getPageMenu());
$oTemplate->setPageContent('page_main_code', $oPage->getPageCode());
$oTemplate->addCss($oPage->getPageCss());
$oTemplate->addJs($oPage->getPageJs());
$oTemplate->getPageCode();
/** @} */
