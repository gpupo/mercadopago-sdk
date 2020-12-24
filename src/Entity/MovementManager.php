<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Entity;

use Gpupo\CommonSchema\ArrayCollection\Banking\Movement\Movement as AC;
use Gpupo\CommonSdk\Entity\Metadata\MetadataContainer;

class MovementManager extends GenericManager
{
    const SEARCH_FUNCTION_ENDPOINT = '/mercadopago_account/movements/search?access_token={access_token}&';

    public function searchByType($type)
    {
        return $this->getFromRoute(['GET', self::SEARCH_FUNCTION_ENDPOINT.sprintf('type=%s&offset={offset}&limit={limit}', $type)]);
    }

    public function getBalance()
    {
        return $this->getFromRoute(['GET', '/users/{user_id}/mercadopago_account/balance?access_token={access_token}']);
    }

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
        $response = $this->getFromRoute(['GET', sprintf('/v1/payments/%s?access_token={access_token}', $id)]);
        $translator = new PaymentTranslator();
        $translator->setNative($response);
        $payment = $translator->doExport();

        return $this->factoryORM($payment, 'Entity\Trading\Order\Shipping\Payment\Payment');
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
