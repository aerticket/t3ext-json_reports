<?php
declare(strict_types=1);

namespace Mindscreen\JsonReports\Output;

abstract class AbstractOutput implements OutputInterface
{

    /**
     * @var array<string, array<StatusItem>>
     */
    protected $reportData;

    /**
     * @param array<string, array<StatusItem>> $reportData
     */
    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

}
