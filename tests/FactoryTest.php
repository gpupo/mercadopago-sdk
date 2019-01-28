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

namespace Gpupo\MercadopagoSdk\Tests;

use Gpupo\CommonSdk\Tests\FactoryTestAbstract;
use Gpupo\MercadopagoSdk\Client\Client;
use Gpupo\MercadopagoSdk\Entity\GenericManager;
use Gpupo\MercadopagoSdk\Entity\MovementManager;
use Gpupo\MercadopagoSdk\Entity\PaymentTranslator;
use Gpupo\MercadopagoSdk\Factory;

/**
 * @coversNothing
 */
class FactoryTest extends FactoryTestAbstract
{
    public $namespace = '\Gpupo\MercadopagoSdk\\';

    public function getFactory()
    {
        return Factory::getInstance();
    }

    public function testSimpleInstance()
    {
        $factory = new \Gpupo\MercadopagoSdk\Factory();
        $manager = $factory->factoryManager('movement');
        $this->assertInstanceOf(MovementManager::class, $manager);
    }

    /**
     * Dá acesso a ``Factory``.
     */
    public function testSetClient()
    {
        $factory = new Factory();

        $factory->setClient([
        ]);

        $this->assertInstanceOf(Client::class, $factory->getClient());
    }

    /**
     * @dataProvider dataProviderManager
     *
     * @param mixed $objectExpected
     * @param mixed $target
     */
    public function testCentralizaAcessoAManagers($objectExpected, $target)
    {
        return $this->assertInstanceOf(
            $objectExpected,
            $this->createObject($this->getFactory(), 'factoryManager', $target)
        );
    }

    public function dataProviderObjetos()
    {
        return [
            [PaymentTranslator::class, 'paymentTranslator', []],
        ];
    }

    public function dataProviderManager()
    {
        return [
            [MovementManager::class, 'movement'],
            [GenericManager::class, 'generic'],
        ];
    }
}
