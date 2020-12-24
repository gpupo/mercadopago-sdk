<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Entity\Banking;

use Gpupo\Common\Entity\ArrayCollection;
use Gpupo\CommonSchema\ArrayCollection\Banking\Report\Record;
use Gpupo\CommonSchema\ArrayCollection\Banking\Report\Report;
use Gpupo\CommonSchema\ORM\Entity\EntityInterface;
use Gpupo\CommonSdk\Exception\ManagerException;
use Gpupo\MercadopagoSdk\Entity\GenericManager;
use Symfony\Component\Console\Output\OutputInterface;

class BankingManager extends GenericManager
{
    public function requestReport()
    {
        return $this->getFromRoute(['POST', '/v1/account/bank_report?access_token={access_token}'], null, [
            'begin_date' => '2017-05-01T03:00:00Z',
            'end_date' => '2017-07-11T02:59:59Z',
        ]);
    }

    public function getReportList(): ArrayCollection
    {
        $list = $this->getFromRoute(['GET', '/v1/account/bank_report/list?access_token={access_token}']);
        $collection = new ArrayCollection();
        foreach ($list as $array) {
            $translated = $this->translateReportDataToCommon($array);
            $report = new Report($translated);
            $collection->add($this->factoryORM($report, 'Entity\Banking\Report\Report'));
        }

        return $collection;
    }

    public function fillReport(EntityInterface $report, OutputInterface $output = null)
    {
        $map = $this->factorySimpleMap(['GET', sprintf('/v1/account/bank_report/%s?access_token={access_token}', $report->getFileName())]);
        $destination = sprintf('var/cache/%s', $report->getFileName());

        if ($output) {
            $output->writeln(sprintf('Opening Report %s ...', $destination));
        }

        if (!file_exists($destination)) {
            if ($output) {
                $output->writeln(sprintf('Requesting remote Report %s ...', $destination));
            }
            $this->getClient()->downloadFile($map->getResource(), $destination);
        }

        $lines = file($destination, FILE_IGNORE_NEW_LINES);

        if (empty($lines)) {
            throw new ManagerException('Empty Report');
        }

        $keys = $this->resolveKeysFromHeader(array_shift($lines));
        $totalCollection = [];

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
                } elseif (\in_array($rac->getDescription(), ['withdrawal', 'reserve_for_payment'], true)) {
                    if ('withdrawal' === $rac->getDescription()) {
                        $totalCollection['withdrawal_fee'] = ((float) $rac->getFeeAmount() * -1);
                    }

                    $report->addExpand($rac->getDescription(), $rac->getExpands());
                } elseif (0 === $rac->getSourceId()) {
                    $errors['unknow'][] = $rac->getExpands();
                } elseif (0 === (int) $rac->getGrossAmount()) {
                    $errors['gross_amount_zero'][] = $rac->getExpands();
                } else {
                    $record = $this->factoryORM($rac, 'Entity\Banking\Report\Record');
                    $record->setReport($report);
                    $report->addRecord($record);
                }

                $report->addExpand('errors', $errors);
            } elseif (\array_key_exists('record_type', $line) && \in_array($line['record_type'], ['subtotal', 'total'], true)) {
                foreach (['date',
                    'source_id',
                    'external_id',
                    'installments',
                    'payment_method',
                    'financing_fee_amount',
                    'taxes_amount',
                    'coupon_amount',
                ] as $d) {
                    unset($line[$d]);
                }

                foreach (['net_debit_amount',
                    'net_credit_amount',
                    'gross_amount',
                    'fee_amount',
                    'shipping_fee_amount',
                ] as $d) {
                    $line[$d] = (float) $line[$d];
                }

                $subtotalKey = sprintf('%s%s', $line['record_type'], (empty($line['description']) ? '' : '_').$line['description']);
                $totalCollection[$subtotalKey] = $line;
            }
        }

        if (!\array_key_exists('total', $totalCollection)) {
            throw new ManagerException('Report with unknow format');
        }
        $totalCollection['total_net'] = $totalCollection['total']['net_credit_amount'] - $totalCollection['subtotal_unblock']['net_credit_amount'] - $totalCollection['withdrawal_fee'];
        $report->addExpand('totalisations', $totalCollection);

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

    protected function translateRecordDataToCommon(array $array, EntityInterface $report): array
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
            ], mb_strtolower($value));
            $keys[] = $key;
        }

        return $keys;
    }
}
