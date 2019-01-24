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

namespace Gpupo\MercadopagoSdk;

use Gpupo\CommonSdk\FactoryAbstract;
use Gpupo\CommonSdk\FactoryInterface;
use Gpupo\MercadopagoSdk\Client\Client;
use Gpupo\MercadopagoSdk\Entity\GenericManager;

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
        return  '\\'.__NAMESPACE__.'\Entity\\';
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
