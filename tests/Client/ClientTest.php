<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Tests\Client;

use Gpupo\CommonSdk\Client\ClientInterface;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;

/**
 * @coversNothing
 */
class ClientTest extends TestCaseAbstract
{
    /**
     * @covers \Gpupo\MercadopagoSdk\Client\Client::getDefaultOptions
     * @covers \Gpupo\MercadopagoSdk\Client\Client::renderAuthorization
     */
    public function testSucessoAoDefinirOptions()
    {
        $client = $this->factoryClient();
        $this->assertInstanceOf(ClientInterface::class, $client);

        return $client;
    }

    /**
     * @depends testSucessoAoDefinirOptions
     */
    public function testGerenciaUriDeRecurso(ClientInterface $client)
    {
        $this->assertSame(
            'https://api.mercadopago.com/foo',
            $client->getResourceUri('/foo')
        );
    }
}
