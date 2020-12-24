<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Client;

use Gpupo\CommonSchema\ArrayCollection\Application\API\OAuth\Client\AccessToken;
use Gpupo\CommonSdk\Client\ClientAbstract;
use Gpupo\CommonSdk\Client\ClientInterface;

final class Client extends ClientAbstract implements ClientInterface
{
    const ENDPOINT = 'api.mercadopago.com';

    const ACCEPT_DEFAULT = 'application/json';

    public function requestToken()
    {
        if ($this->getOptions()->get('client_refresh_token') && !empty($this->getOptions()->get('client_refresh_token'))) {
            $pars = [
                'grant_type' => 'refresh_token',
                'client_id' => $this->getOptions()->get('client_id'),
                'client_secret' => $this->getOptions()->get('client_secret'),
                'refresh_token' => $this->getOptions()->get('client_refresh_token'),
            ];
        } else {
            $pars = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->getOptions()->get('client_id'),
                'client_secret' => $this->getOptions()->get('client_secret'),
            ];
        }

        $this->setMode('form');
        $request = $this->post($this->getOauthUrl('/token'), $pars);
        $accessToken = $request->getData(AccessToken::class);

        return $accessToken;
    }

    protected function renderAuthorization(): array
    {
        return [];
    }

    protected function getOauthUrl($path)
    {
        return $this->getOptions()->get('oauth_url').$path;
    }
}
