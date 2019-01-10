<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    BaseProfile Base classes for profile modules
 * @ingroup     TridentModules
 *
 * @{
 */

class BxBaseModProfileSearchResult extends BxBaseModGeneralSearchResult
{
    public function __construct($sMode = '', $aParams = array())
    {
        parent::__construct($sMode, $aParams);
        $this->sCenterContentUnitSelector = '.bx-base-pofile-unit';
    }

    protected function _setConnectionsConditions ($aParams)
    {
        $oConnection = isset($aParams['object']) ? BxDolConnection::getObjectInstance($aParams['object']) : false;
        if (!$oConnection || !isset($aParams['profile']) || !(int)$aParams['profile'])
            return false;

        $sContentType = isset($aParams['type']) ? $aParams['type'] : BX_CONNECTIONS_CONTENT_TYPE_CONTENT;
        $isMutual = isset($aParams['mutual']) ? $aParams['mutual'] : false;
        $a = $oConnection->getConnectionsAsCondition ($sContentType, 'id', (int)$aParams['profile'], (int)$aParams['profile2'], $isMutual);

        $this->aCurrent['restriction'] = array_merge($this->aCurrent['restriction'], $a['restriction']);
        $this->aCurrent['join'] = array_merge($this->aCurrent['join'], $a['join']);

        return true;
    }

	protected function _setAclConditions ($aParams)
    {
        $oAcl =  BxDolAcl::getInstance();
        if(!$oAcl || empty($aParams['level']))
            return false;

		if(!is_array($aParams['level']))
			$aParams['level'] = array($aParams['level']);

		$this->aCurrent['title'] = array();
		foreach($aParams['level'] as $iLevelId) {
			$aInfo = $oAcl->getMembershipInfo($iLevelId);
			if(empty($aInfo) || !is_array($aInfo))
				continue;

			$this->aCurrent['title'][] = _t($aInfo['name']);
		}

        $aCondition = $oAcl->getContentByLevelAsCondition('id', $aParams['level']);        
        $this->aCurrent['restriction_sql'] = (!empty($this->aCurrent['restriction_sql']) ? $this->aCurrent['restriction_sql'] : '') . $aCondition['restriction_sql'];
        $this->aCurrent['restriction'] = array_merge($this->aCurrent['restriction'], $aCondition['restriction']);
        $this->aCurrent['join'] = array_merge($this->aCurrent['join'], $aCondition['join']);

        return true;
    }
}

/** @} */
