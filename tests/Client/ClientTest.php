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

    public function testAccessToken()
    {
        $headerList = $this->factoryClient()->factoryRequest('/items')->getHeader();
        $this->assertSame('Bearer fooToken', $headerList['Authorization']);
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
