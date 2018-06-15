<?php

declare(strict_types=1);

/*
 * This file is part of <hummer app>
 * @copyright 2018 Copyright (C) Novo Varejo Ponto Com - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @version $version$ Hummer
 *
 */

namespace Gpupo\MercadopagoSdk\Translator;

use Gpupo\CommonSchema\AbstractTranslator;
use Gpupo\CommonSchema\ArrayCollection\Trading\Payment\Payment;
use Gpupo\CommonSchema\ArrayCollection\Trading\Payment\Collector\Collector;
use Gpupo\CommonSchema\TranslatorDataCollection;
use Gpupo\CommonSchema\TranslatorException;
use Gpupo\CommonSchema\TranslatorInterface;
use Gpupo\CommonSdk\Traits\LoadTrait;

class Translator extends AbstractTranslator implements TranslatorInterface
{
    use LoadTrait;

    public function translateFrom() {}

    public function translateTo()
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
