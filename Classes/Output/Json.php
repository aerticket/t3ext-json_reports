<?php
declare(strict_types=1);

namespace Mindscreen\JsonReports\Output;

class Json extends AbstractOutput
{

    public function getText(): string
    {
        return json_encode($this->reportData, JSON_THROW_ON_ERROR);
    }

    public function getExitCode(): int
    {
        return 0;
    }


}
