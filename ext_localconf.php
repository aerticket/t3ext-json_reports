<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output'])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output'] = [];
}
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output']['json'] = \Mindscreen\JsonReports\Output\Json::class;
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output']['nagios'] = \Mindscreen\JsonReports\Output\Nagios::class;

// Define default report group that does not exclude any reports
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['groups']['default'] = [
    'include' => ['*'],
    'exclude' => [],
];
