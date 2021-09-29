<?php
declare(strict_types=1);

namespace Mindscreen\JsonReports\Output;

use TYPO3\CMS\Reports\Status;

class Nagios extends AbstractOutput
{

    /**
     * @var array<string|int, int>
     */
    protected $count = [
        '-2' => 0,
        '-1' => 0,
        '0' => 0,
        '1' => 0,
        '2' => 0,
    ];

    /**
     * @var array<mixed>
     */
    protected $messages = [];

    public function __construct(array $reportData)
    {
        parent::__construct($reportData);

        $warnings = [];
        foreach ($reportData as $reportCategory) {
            foreach ($reportCategory as $status) {
                $this->count[(string)$status->getSeverity()]++;
                if ($status->getSeverity() === (string)Status::ERROR) {
                    $this->messages[] = $status->getTitle() . ': ' . $status->getValue() . ';';
                } elseif ($status->getSeverity() === (string)Status::WARNING) {
                    $warnings[] = $status->getTitle() . ': ' . $status->getValue() . ';';
                }
            }
        }
        $this->messages = array_merge($this->messages, $warnings);
    }

    public function getText(): string
    {
        if ($this->count['1'] === 0 && $this->count['2'] === 0) {
            $textOutput = 'No warnings or errors in TYPO3 reports';
        } elseif ($this->count['2'] === 0) {
            $textOutput = sprintf('%s warning(s) in TYPO3 reports', $this->count['1']);
        } else {
            $textOutput = sprintf('%s error(s) in TYPO3 reports', $this->count['2']);
        }

        $performanceData = sprintf('NOTICE=%s INFO=%s OK=%s WARNING=%s ERROR=%s', $this->count['-2'], $this->count['-1'], $this->count['0'], $this->count['1'], $this->count['2']);

        return $textOutput . ' | ' . $performanceData . PHP_EOL . implode(PHP_EOL, $this->messages);
    }

    public function getExitCode(): int
    {
        if ($this->count['1'] === 0 && $this->count['2'] === 0) {
            $returnCode = 0;
        } elseif ($this->count['2'] === 0) {
            $returnCode = 1;
        } else {
            $returnCode = 2;
        }
        return $returnCode;
    }

}
