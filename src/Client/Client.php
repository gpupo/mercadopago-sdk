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

use Gpupo\CommonSchema\ArrayCollection\Application\API\OAuth\Client\AccessToken;
use Gpupo\CommonSdk\Client\ClientAbstract;
use Gpupo\CommonSdk\Client\ClientInterface;

final class Client extends ClientAbstract implements ClientInterface
{
    const ENDPOINT = 'api.mercadopago.com';

    const ACCEPT_DEFAULT = 'application/json';

    protected $header_access_token = true;
    
    protected function factoryTokenBodyParameters(): array
    {
        //Client Support
        $clientRefreshToken = $this->getOptions()->get('client_refresh_token');
        if (!empty($clientRefreshToken)) {
            return [
                'grant_type' => 'refresh_token',
                'client_id' => $this->getOptions()->get('client_id'),
                'client_secret' => $this->getOptions()->get('client_secret'),
                'refresh_token' => $clientRefreshToken,
            ];
        }

        return [
            'grant_type' => 'client_credentials',
            'client_id' => $this->getOptions()->get('client_id'),
            'client_secret' => $this->getOptions()->get('client_secret'),
        ];
    }

    public function requestToken()
    {
        $this->setMode('form');
        $this->header_access_token = false;
        
        try {
            $request = $this->post($this->getOauthUrl('/token'), $this->factoryTokenBodyParameters());
        } catch (\Exception $exception) {
            $this->header_access_token = true;
            throw $exception;
        }
        
        $this->header_access_token = true;
        $accessToken = $request->getData(AccessToken::class);

        return $accessToken;
    }

    protected function renderAuthorization(): array
    {
        if(false === $this->header_access_token) {
            return [];
        }
        
        return [
            'Authorization' => sprintf('Bearer %s', $this->getOptions()->get('access_token')),
        ];
    }

    protected function getOauthUrl($path)
    {
        return $this->getOptions()->get('oauth_url').$path;
    }
}
