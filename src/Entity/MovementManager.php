<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Entity;

use Gpupo\CommonSchema\ArrayCollection\Banking\Movement\Movement as AC;
use Gpupo\CommonSchema\ArrayCollection\Banking\Movement\Report;
use Gpupo\CommonSchema\ORM\Entity\EntityInterface;
use Gpupo\CommonSdk\Entity\Metadata\MetadataContainer;
use Gpupo\Common\Entity\Collection;
use Gpupo\MercadopagoSdk\Traits\CsvFileProcessTrait;
use Gpupo\MercadopagoSdk\Traits\ReportFactoryTrait;
use Symfony\Component\Console\Output\OutputInterface;

class MovementManager extends GenericManager
{
    use CsvFileProcessTrait;
    use ReportFactoryTrait;

    const REPORT_ORM_CLASS = 'Entity\Banking\Movement\Report';
    const REPORT_ARRAY_COLLECTION_CLASS = Report::class;

    const SEARCH_FUNCTION_ENDPOINT = '/mercadopago_account/movements/search?';
    const SETTLEMENT_REPORT_ENDPOINT = '/v1/account/settlement_report';

    public function searchByType($type)
    {
        return $this->getFromRoute(['GET', self::SEARCH_FUNCTION_ENDPOINT.sprintf('type=%s&offset={offset}&limit={limit}', $type)]);
    }

    public function getBalance()
    {
        return $this->getFromRoute(['GET', '/users/{user_id}/mercadopago_account/balance']);
    }

    public function getReportList(): Collection
    {
        $list = $this->getFromRoute(['GET', self::SETTLEMENT_REPORT_ENDPOINT . '/list']);

        return $this->factoryReportsFromList($list);
    }

    public function fillReport(EntityInterface $report, OutputInterface $output = null): EntityInterface
    {
        $lines = $this->fetchCsvFileLines($report, self::SETTLEMENT_REPORT_ENDPOINT, $output);
        $keys = $this->resolveKeysFromHeader(array_shift($lines), false);
        $final_keys = $this->replaceKeysFromHeader($keys, [
            'source_id' => 'id',
            'payment_method_type' => 'financial_entity',
            'transaction_type' => 'type',
            'settlement_net_amount' => 'amount',
            'settlement_currency' => 'currency_id',
            'transaction_date' => 'date_created',
            'settlement_date' => 'date_released',
            'external_reference' => 'reference_id',
        ]);

        foreach($lines as $raw_line) {
            $line = str_getcsv($raw_line, $this->separator);
            $final_line = array_combine($final_keys, $line);
            $translated_data = $this->translateMovementDataToCommon($final_line);
            $ac = new AC($translated_data);
            $movement = $this->factoryORM($ac, 'Entity\Banking\Movement\Movement');
            $movement->setReport($report);
            $report->addMovement($movement);
        }

        return $report;
    }

    /**
     * @deprecated
     */
    public function getMovementList(int $days_ago = 7): MetadataContainer
    {
        $list = $this->getFromRoute(
            [
                'GET',
                self::SEARCH_FUNCTION_ENDPOINT.'range={range}&begin_date={begin_date}&end_date={end_date}&offset={offset}&limit={limit}',
            ],
            [
                'range' => 'date_created',
                'begin_date' => sprintf('NOW-%dDAY', $days_ago),
                'end_date' => 'NOW',
            ]
        );

        $collection = new MetadataContainer();
        $collection->getMetadata()
            ->setOffset($list['paging']['offset'])
            ->setLimit($list['paging']['limit'])
            ->setTotalRows($list['paging']['total']);

        if (!$list->getResults()) {
            $collection->clear();

            return $collection;
        }

        foreach ($list->getResults() as $array) {
            $translated = $this->translateMovementDataToCommon($array);
            $ac = new AC($translated);
            $movement = $this->factoryORM($ac, 'Entity\Banking\Movement\Movement');
            $collection->add($movement);
        }

        return $collection;
    }

    /**
     * @see https://www.mercadopago.com.br/developers/pt/reference/payments/resource/
     *
     * @param mixed $id
     */
    public function findPaymentById($id)
    {
        $response = $this->getFromRoute(['GET', sprintf('/v1/payments/%s', $id)]);
        $translator = new PaymentTranslator();
        $translator->setNative($response);
        $payment = $translator->doExport();

        return $this->factoryORM($payment, 'Entity\Trading\Order\Shipping\Payment\Payment');
    }

    public function getPaymentList(int $days_ago = 7, $offset = 0, $limit = 50)
    {
        $list = $this->getFromRoute(
            [
                'GET',
                '/v1/payments/search?range={range}&begin_date={begin_date}&end_date={end_date}&offset={offset}&limit={limit}',
            ],
            [
                'range' => 'date_last_updated',
                'begin_date' => sprintf('NOW-%dDAYS', $days_ago),
                'end_date' => 'NOW',
                'offset' => $offset,
                'limit' => $limit,
            ]
        );

        $collection = new MetadataContainer();

        if (!$list->getResults()) {
            $collection->clear();

            return $collection;
        }

        foreach ($list->getResults() as $a) {
            $array = new Collection($a);
            $translator = new PaymentTranslator();
            $translator->setNative($array);
            $payment = $translator->doExport();

            $collection->add($this->factoryORM($payment, 'Entity\Trading\Order\Shipping\Payment\Payment'));
        }

        if ($list['paging']['total'] > ($list['paging']['offset'] + $list['paging']['limit'])) {
            foreach ($this->getPaymentList($days_ago, $offset+$limit, $limit) as $item) {
                $collection->add($item);
            }
        }
        
        return $collection;
    }

    protected function translateMovementDataToCommon(array $array): array
    {
        $translated = array_merge([
            'move_id' => $array['id'],
            'payment_id' => $array['reference_id'],
            'state' => $array['status'],
            'expands' => $array,
        ], $array);

        return $translated;
    }
}
