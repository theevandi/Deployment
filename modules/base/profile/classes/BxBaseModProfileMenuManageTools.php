<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 *
 * @defgroup    BaseProfile Base classes for profile modules
 * @ingroup     UnaModules
 *
 * @{
 */

/**
 * Manage tools submenu
 */
class BxBaseModProfileMenuManageTools extends BxBaseModGeneralMenuManageTools
{

    public function __construct($aObject, $oTemplate = false)
    {
        parent::__construct($aObject, $oTemplate);
    }

	/**
     * Check if menu items is visible.
     * @param $a menu item array
     * @return boolean
     */
    protected function _isVisible ($a)
    {
        if(!parent::_isVisible($a))
            return false;

        if(empty($this->_aContentInfo) || !is_array($this->_aContentInfo))
        	return false;

        $sCheckFuncName = '';
        switch ($a['name']) {
            case 'delete':
            case 'delete-with-content':
            	if($this->_oModule->checkMyself($this->_iContentId))
            		return false;

                $sCheckFuncName = 'checkAllowedDelete';
                break;
        }

        if(!$sCheckFuncName || !method_exists($this->_oModule, $sCheckFuncName))
            return true;

        return $this->_oModule->{$sCheckFuncName}($this->_aContentInfo) === CHECK_ACTION_RESULT_ALLOWED;
    }
}

/** @} */
