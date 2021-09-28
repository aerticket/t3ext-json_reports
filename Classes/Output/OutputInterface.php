<?php
declare(strict_types=1);

namespace Mindscreen\JsonReports\Output;

interface OutputInterface
{

    /**
     * @param array<string, array<StatusItem>> $reportData
     */
    public function __construct(array $reportData);

    public function getText(): string;

    public function getExitCode(): int;
}
