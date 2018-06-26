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
use Gpupo\MercadopagoSdk\Client\Client;

/**
 * Construtor principal, estendido pelo Factory de MarkethubBundle.
 */
class Factory extends FactoryAbstract
{
    public function setClient(array $clientOptions = [])
    {
        $this->client = new Client($clientOptions, $this->getLogger(), $this->getSimpleCache());
    }

    public function getNamespace()
    {
        return  '\\'.__NAMESPACE__.'\Entity\\';
    }

    protected function getSchema($namespace = null)
    {
        return [
            'generic' => [
                'manager' => sprintf('%sGenericManager', $namespace),
            ],
            'movement' => [
                'manager' => sprintf('%sMovementManager', $namespace),
            ],
            'banking' => [
                'manager' => sprintf('%sBanking\BankingManager', $namespace),
            ],
            'paymentTranslator' => [
                'class' => sprintf('%sPaymentTranslator', $namespace),
            ],
        ];
    }
}
