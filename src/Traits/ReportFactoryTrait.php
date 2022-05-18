<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Traits;

use Gpupo\Common\Entity\Collection;
use Gpupo\CommonSchema\ORM\Entity\EntityInterface;
use Gpupo\CommonSchema\ArrayCollection\Banking\Movement\Report;

/**
 * Save common logic for mp csv reports.
 */
trait ReportFactoryTrait
{
    public function factoryReportsFromList(Collection $reports): Collection
    {
        return $reports->map(function($report_data) {
            return $this->factoryReport($report_data);
        });
    }

    public function factoryReport(array $reportData): EntityInterface
    {
        if (!defined('static::REPORT_ARRAY_COLLECTION_CLASS')) {
            throw new \LogicException('Constant with Report collection class not defined!');
        }

        if (!defined('static::REPORT_ORM_CLASS')) {
            throw new \LogicException('Constant with Report orm class not defined!');
        }
        $translated = $this->translateReportDataToCommon($reportData);
        $className = static::REPORT_ARRAY_COLLECTION_CLASS;
        $report = new $className($translated);

        return $this->factoryORM($report, static::REPORT_ORM_CLASS);
    }

    protected function translateReportDataToCommon(array $array): array
    {
        $translated = array_merge([
            'institution' => 'mercadopago',
            'generated_date' => $array['date_created'],
            'external_id' => $array['id'],
            'description' => $array['created_from'],
            'tags' => current(explode('_', $array['created_from'])),
            'expands' => $array,
        ], $array);

        return $translated;
    }

    public function getReportConfig(): array
    {
        $this->assertConfigReportEndpointConstExists();
        if ($config = $this->getFromRoute(['GET', static::REPORT_URL_CONFIG_ENDPOINT])) {
            return $config->toArray();
        }

        return [];
    }

    protected function updateReportConfig(array $config): array
    {
        $this->assertConfigReportEndpointConstExists();
        if (empty($result = $this->getFromRoute(['PUT', static::REPORT_URL_CONFIG_ENDPOINT], null, json_encode($config)))) {
            return [];
        }

        return $result->toArray();
    }

    private function assertConfigReportEndpointConstExists(): void
    {
        if (!\defined('static::REPORT_URL_CONFIG_ENDPOINT')) {
            throw new \LogicException('Constant "REPORT_URL_CONFIG_ENDPOINT" doesn\'t defined in class: '.get_class($this), 503);
        }
    }

    public function enableScheduleReport(string $frequency = 'daily'): bool
    {
        if(!in_array($frequency, ['daily', 'weekly', 'monthly'])) {
            throw new \LogicException('Frequency value should be "daily", "weekly" and "monthly"; receive: "' . $frequency . '"', 503);
        }

        if (empty($old_config = $this->getReportConfig())) {
            return false;
        }

        if (($old_config['scheduled'] ?? false) 
            && isset($old_config['frequency']['type']) 
            && $old_config['frequency']['type'] === $frequency
        ) {
            return true;
        }

        $success = true;
        if (isset($old_config['frequency']) && $old_config['frequency'] && $frequency !== $old_config['frequency']['type']) {
            $new_config = $old_config;
            $new_config['frequency'] = [];
            $new_config['frequency']['type'] = $frequency;
            $new_config['frequency']['hour'] = 23;

            $success = !empty($this->updateReportConfig($new_config));
        }

        if (!defined('static::REPORT_ENABLE_SCHEDULED_ENDPOINT')) {
            throw new \LogicException(
                'Constant "REPORT_ENABLE_SCHEDULED_ENDPOINT" not defined in class: '
                    . get_class($this)
            , 503);
        }

        $result = $this->getFromRoute(['POST', static::REPORT_ENABLE_SCHEDULED_ENDPOINT]);

        return $success && !empty($result->toArray());
    }

    public function requestReport(\DateTimeImmutable $endDate, string $interval = 'daily')
    {
        if (!defined('static::REPORT_CREATE_MANUAL_ENDPOINT')) {
            throw new \LogicException(
                'There\'s no constant "REPORT_CREATE_MANUAL_ENDPOINT" defined in "'
                    . get_class($this) 
                    . '"');
        }

        $date_interval = $this->factoryReportDateInterval($interval);
        $begin_date = $endDate->sub($date_interval);

        $begin_date_str = $begin_date->format('Y-m-d\T00:00:00\Z');
        $end_date_str = $endDate->format('Y-m-d\T00:00:00\Z');

        return $this->getFromRoute(['POST', static::REPORT_CREATE_MANUAL_ENDPOINT], null, json_encode([
            'begin_date' => $begin_date_str,
            'end_date' => $end_date_str,
        ]));
    }

    protected function factoryReportDateInterval(string $interval): \DateInterval
    {
        if ('monthly' === $interval) {
            return new \DateInterval('P1M');
        }

        if ('weekly' === $interval) {
            return new \DateInterval('P7D');
        }

        if ('daily' === $interval) {
            return new \DateInterval('P1D');
        }

        throw new \LogicException('Interval "' . $interval . '" is invalid! Use "monthly", "weekly" and "daily"');
    }
}

