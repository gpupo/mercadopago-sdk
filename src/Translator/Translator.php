<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Translator;

use Gpupo\CommonSchema\AbstractTranslator;
use Gpupo\CommonSchema\ArrayCollection\Trading\Order\Shipping\Payment\Payment;
use Gpupo\CommonSchema\TranslatorDataCollection;
use Gpupo\CommonSchema\TranslatorException;
use Gpupo\CommonSchema\TranslatorInterface;
use Gpupo\CommonSdk\Traits\LoadTrait;

class Translator extends AbstractTranslator implements TranslatorInterface
{
    use LoadTrait;

    public function import()
    {
    }

    public function export()
    {
        if (!$this->getNative() instanceof TranslatorDataCollection) {
            throw new TranslatorException('Native missed!');
        }
        $map = $this->loadMap('native');

        return new Payment($map);
    }

    private function loadMap($name)
    {
        $file = __DIR__.'/map/translate.'.$name.'.map.php';
        $method = 'get'.ucfirst($name);
        $pars = [$name => $this->{$method}()];

        return $this->loadArrayFromFile($file, $pars);
    }
}
