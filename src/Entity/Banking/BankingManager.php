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

use Gpupo\CommonSchema\ArrayCollection\Banking\Report\Report;
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
        return $this->getFromRoute(['GET', '/v1/account/bank_report/list?access_token={access_token}']);
    }

    public function findReportById($filename)
    {
        $map = $this->factorySimpleMap(['GET', sprintf('/v1/account/bank_report/%s?access_token={access_token}', $filename)]);
        $destination = sprintf('var/cache/%s', $filename);

        if (!file_exists($destination)) {
            $this->getClient()->downloadFile($map->getResource(), $destination);
        }

        $lines = file($destination, FILE_IGNORE_NEW_LINES);

        if (empty($lines)) {
            throw new \Exception('Empty Report');
        }

        $keys = $this->resolveKeysFromHeader(array_shift($lines));

        $report = new Report([
            'file_name' => $filename,
            'institution' => 'mercadopago',
        ]);

        foreach ($lines as $value) {
            $line = [
            ];
            foreach (str_getcsv($value) as $k => $v) {
                $line[$keys[$k]] = $v;
            }

            if (!empty($line['date'])) {
                $report->getRecords()->factoryElementAndAdd($line);
            }
        }

        return $this->setConversionType('ORM')->decorateByConversionType($report);
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
