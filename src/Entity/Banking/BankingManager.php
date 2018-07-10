<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk
 * Created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 * Para a informação dos direitos autorais e de licença você deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 * For more information, see <https://opensource.gpupo.com/>.
 *
 */

namespace Gpupo\MercadopagoSdk\Entity\Banking;

use Gpupo\Common\Entity\ArrayCollection;
use Gpupo\CommonSchema\ArrayCollection\Banking\Report\Record;
use Gpupo\CommonSchema\ArrayCollection\Banking\Report\Report as ReportAC;
use Gpupo\CommonSchema\ORM\Entity\Banking\Report\Report;
use Gpupo\CommonSdk\Exception\ManagerException;
use Gpupo\MercadopagoSdk\Entity\GenericManager;

class BankingManager extends GenericManager
{
    public function requestReport()
    {
        return $this->getFromRoute(['POST', '/v1/account/bank_report?access_token={access_token}'], null, [
            'begin_date' => '2017-05-01T03:00:00Z',
            'end_date' => '2017-07-11T02:59:59Z',
        ]);
    }

    public function getReportList()
    {
        $list = $this->getFromRoute(['GET', '/v1/account/bank_report/list?access_token={access_token}']);
        $collection = new ArrayCollection();
        foreach ($list as $array) {
            $translated = $this->translateReportDataToCommon($array);
            $report = new ReportAC($translated);
            $collection->add($report);
        }

        return $collection;
    }

    public function fillReport(Report $report)
    {
        $map = $this->factorySimpleMap(['GET', sprintf('/v1/account/bank_report/%s?access_token={access_token}', $report->getFileName())]);
        $destination = sprintf('var/cache/%s', $report->getFileName());

        if (!file_exists($destination)) {
            $this->getClient()->downloadFile($map->getResource(), $destination);
        }

        $lines = file($destination, FILE_IGNORE_NEW_LINES);

        if (empty($lines)) {
            throw new ManagerException('Empty Report');
        }

        $keys = $this->resolveKeysFromHeader(array_shift($lines));

        foreach ($lines as $value) {
            $line = [
            ];
            foreach (str_getcsv($value) as $k => $v) {
                $line[$keys[$k]] = $v;
            }

            $errors = [];

            if (!empty($line['date'])) {
                $translatedLine = $this->translateRecordDataToCommon($line, $report);
                $rac = new Record($translatedLine);
                if ('initial_available_balance' === $rac->getRecordType()) {
                    $report->addExpand('initial_available_balance', $rac->getExpands());
                } elseif (in_array($rac->getDescription(), ['withdrawal', 'reserve_for_payment'], true)) {
                    $report->addExpand($rac->getDescription(), $rac->getExpands());
                } elseif (0 === $rac->getSourceId()) {
                    $errors['unknow'][] = $rac->getExpands();
                } else {
                    $record = $rac->toOrm();
                    $record->setReport($report);
                    $report->addRecord($record);
                }

                $report->addExpand('errors', $errors);
            }
        }

        return $this->decorateByConversionType($report);
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

    protected function translateRecordDataToCommon(array $array, Report $report): array
    {
        $translated = array_merge([
            'tags' => [$report->getDescription()],
            'expands' => $array,
        ], $array);

        return $translated;
    }

    protected function resolveKeysFromHeader($array)
    {
        $keys = [];

        foreach (str_getcsv($array) as $value) {
            $key = str_replace([
                'mp_',
                'reference',
            ], [
                '',
                'id',
            ], strtolower($value));
            $keys[] = $key;
        }

        return $keys;
    }
}
