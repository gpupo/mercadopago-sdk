<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Entity;

use Gpupo\Common\Entity\CollectionInterface;
use Gpupo\CommonSchema\AbstractTranslator;
use Gpupo\CommonSchema\ArrayCollection\Trading\Order\Shipping\Payment\Payment;
use Gpupo\CommonSdk\Traits\LoadTrait;

class PaymentTranslator extends AbstractTranslator
{
    use LoadTrait;

    /**
     * {@inheritdoc}
     */
    public function export()
    {
        if (!$this->getNative() instanceof CollectionInterface) {
            throw new TranslatorException('Mercadopago Payment missed!');
        }

        $data = $this->loadMap('native');

        return new Payment($data);
    }

    /**
     * {@inheritdoc}
     */
    public function import()
    {
        if (!$this->getForeign() instanceof TranslatorDataCollection) {
            throw new TranslatorException('Foreign missed!');
        }

        $data = $this->loadMap('foreign');

        return $data;
    }

    private function loadMap($name)
    {
        $filename = __DIR__.'/map/payment-translator-from-'.$name.'.php';

        if (!file_exists($filename)) {
            throw new \Exception(sprintf('Filename %s not exists!', $filename));
        }

        $method = 'get'.ucfirst($name);

        return $this->loadArrayFromFile($filename, [$name => $this->{$method}()], false);
    }
}
