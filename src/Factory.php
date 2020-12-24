<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk;

use Gpupo\CommonSdk\FactoryAbstract;
use Gpupo\CommonSdk\FactoryInterface;
use Gpupo\MercadopagoSdk\Client\Client;

/**
 * Construtor principal, estendido pelo Factory de MarkethubBundle.
 */
class Factory extends FactoryAbstract implements FactoryInterface
{
    protected $name = 'mercadopago-sdk';

    public function setClient(array $clientOptions = [])
    {
        $this->client = new Client($clientOptions, $this->getLogger(), $this->getSimpleCache());
    }

    public function getNamespace()
    {
        return '\\'.__NAMESPACE__.'\Entity\\';
    }

    protected function getSchema(): array
    {
        return [
            'generic' => [
                'manager' => Entity\GenericManager::class,
            ],
            'movement' => [
                'manager' => Entity\MovementManager::class,
            ],
            'banking' => [
                'manager' => Entity\Banking\BankingManager::class,
            ],
            'paymentTranslator' => [
                'class' => Entity\PaymentTranslator::class,
            ],
        ];
    }
}
