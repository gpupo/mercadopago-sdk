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
            $report = new ReportAC($array);
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
            throw new \Exception('Empty Report');
        }

        $keys = $this->resolveKeysFromHeader(array_shift($lines));

        foreach ($lines as $value) {
            $line = [
            ];
            foreach (str_getcsv($value) as $k => $v) {
                $line[$keys[$k]] = $v;
            }

            if (!empty($line['date'])) {
                $rac = new Record($line);
                $record = $rac->toOrm();
                $record->setReport($report);
                $report->addRecord($record);
            }
        }

        return $this->decorateByConversionType($report);
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
