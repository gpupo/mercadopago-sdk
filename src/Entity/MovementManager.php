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

namespace Gpupo\MercadopagoSdk\Entity;

class MovementManager extends GenericManager
{
    public function search($type)
    {
        return $this->getFromRoute(['GET', sprintf('/mercadopago_account/movements/search?access_token={access_token}&type=%s&offset={offset}&limit={limit}', $type)]);
    }

    public function getBalance()
    {
        return $this->getFromRoute(['GET', '/users/{user_id}/mercadopago_account/balance?access_token={access_token}']);
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
        $payment = $translator->translateToForeign();

        return $payment;
    }
}
