<?php

$mixCheckResult = 'Update can not be applied';

if ('8.0.0-B1' == $this->oDb->getOne("SELECT `version` FROM `sys_modules` WHERE `name` = 'system'"))
    $mixCheckResult = true;

return $mixCheckResult;
