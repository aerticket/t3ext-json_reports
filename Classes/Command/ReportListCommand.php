<?php
declare(strict_types=1);

namespace Mindscreen\JsonReports\Command;

use Mindscreen\JsonReports\Output\OutputInterface as ReportOutputInterface;
use Mindscreen\JsonReports\Output\StatusItem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Reports\Status;
use TYPO3\CMS\Reports\StatusProviderInterface;

class ReportListCommand extends Command
{

    /**
     * @var array<string, array<string, array<string>>>
     */
    protected $groupConfiguration = [];

    protected function configure(): void
    {
        $this->setDescription('Output reports in specified format')
            ->addArgument(
                'format',
                InputArgument::OPTIONAL,
                'The desired output format (defaults to json)',
                'json'
            )
            ->addArgument(
                'group',
                InputArgument::OPTIONAL,
                'The report group to display',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getLanguageService()->includeLLFile('EXT:reports/Resources/Private/Language/locallang_reports.xlf');
        $format = $input->getArgument('format');
        $group = $input->getArgument('group');

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['groups'][$group])) {
            throw new Exception('The report group  "' . $group . '" has not been configured.', 1554465727);
        }
        $this->groupConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['groups'][$group];

        $result = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers'] as $category => $providers) {
            $result[$category] = [];
            foreach ($providers as $providerClass) {
                if (!class_exists($providerClass)) {
                    throw new \Exception(sprintf('Invalid provider class name %s', $providerClass), 1632821882451);
                }
                $provider = GeneralUtility::makeInstance($providerClass);
                if ($provider instanceof StatusProviderInterface) {
                    $statusArray = $provider->getStatus();
                    /** @var Status $status */
                    foreach ($statusArray as $status) {
                        if ($this->isIncludedInGroup($category, $status->getTitle())
                            && !$this->isExcludedFromGroup($category, $status->getTitle())) {
                            $result[$category][] = StatusItem::fromStatus($status);
                        }
                    }
                }
            }
        }

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output'][$format])) {
            throw new Exception('The output class for format "' . $format . '" has not been configured.', 1517161193);
        }
        $outputClass = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['json_reports']['output'][$format];
        if (!class_exists($outputClass)) {
            throw new \Exception(sprintf('Invalid output class name %s', $outputClass), 1632821882452);
        }
        $reportOutput = GeneralUtility::makeInstance($outputClass, $result);
        if (!$reportOutput instanceof ReportOutputInterface) {
            throw new Exception('The output class "' . get_class($reportOutput) . '" does not implement OutputInterface.',
                1517161194);
        }

        $output->write($reportOutput->getText());
        return $reportOutput->getExitCode();
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function isExcludedFromGroup(string $category, string $title): bool
    {
        if (isset($this->groupConfiguration['exclude'][$category])
            && is_array($this->groupConfiguration['exclude'][$category])) {
            if (in_array($title, $this->groupConfiguration['exclude'][$category])) {
                return true;
            }
        }
        return false;
    }

    protected function isIncludedInGroup(string $category, string $title): bool
    {
        if (is_array($this->groupConfiguration['include'])
            && in_array('*', $this->groupConfiguration['include'])) {
            return true;
        }
        if (isset($this->groupConfiguration['include'][$category])
            && is_array($this->groupConfiguration['include'][$category])) {
            if (in_array('*', $this->groupConfiguration['include'][$category])
                || in_array($title, $this->groupConfiguration['include'][$category])) {
                return true;
            }
        }
        return false;
    }
}
