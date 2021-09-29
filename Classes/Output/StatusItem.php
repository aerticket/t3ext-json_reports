<?php
declare(strict_types=1);

namespace Mindscreen\JsonReports\Output;

use TYPO3\CMS\Reports\Status;

class StatusItem implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $severity;

    public function __construct(string $title, string $value, string $message, string $severity)
    {
        $this->title = $title;
        $this->value = $value;
        $this->message = $message;
        $this->severity = $severity;
    }

    public static function fromStatus(Status $status): StatusItem
    {
        return new self(
            $status->getTitle(),
            $status->getValue(),
            $status->getMessage(),
            (string)$status->getSeverity()
        );
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'value' => $this->value,
            'message' => $this->message,
            'severity' => $this->severity,
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }
}
