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
use Gpupo\CommonSchema\ArrayCollection\Banking\Report\Report;

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
        $translated = $this->translateReportDataToCommon($reportData);
        $report = new Report($translated);

        return $this->factoryORM($report, 'Entity\Banking\Report\Report');
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
}

