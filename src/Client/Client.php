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

namespace Gpupo\MercadopagoSdk\Client;

use Gpupo\CommonSdk\Client\ClientAbstract;
use Gpupo\CommonSdk\Client\ClientInterface;

final class Client extends ClientAbstract implements ClientInterface
{
    /**
     * @codeCoverageIgnore
     */
    public function getDefaultOptions()
    {
        $domain = 'api.mercadopago.com';

        return [
            'app_id' => false,
            'secret_key' => false,
            'access_token' => false,
            'refresh_token' => false,
            'users_url' => sprintf('https://%s/users', $domain),
            'base_url' => sprintf('https://%s/v1', $domain),
            'verbose' => true,
            'cacheTTL' => 3600,
            'user_id' => 12345678,
        ];
    }

    protected function renderAuthorization()
    {
        $list = [];

        return $list;
    }
}
