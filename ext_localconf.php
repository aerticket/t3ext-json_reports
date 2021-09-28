<?php

use Mindscreen\JsonReports\Output\Json;
use Mindscreen\JsonReports\Output\Nagios;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Access to the reports script via HTTP is only granted to the allowed IP addresses or address range
if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['allowedIpAddresses'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['allowedIpAddresses'] = '';
}

if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output'] = [];
}
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output']['json'] = Json::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output']['nagios'] = Nagios::class;

// Define default report group that does not exclude any reports
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['groups']['default'] = [
    'include' => ['*'],
    'exclude' => [],
];
