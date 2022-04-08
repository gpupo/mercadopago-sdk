<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Traits;

trait CsvFileProcessTrait
{
    protected $separator;

    protected function resolveKeysFromHeader($line)
    {
        $keys = [];

        $this->separator = ';';
        if (false != strpos($line, ',')) {
            $this->separator = ',';
        }

        foreach (str_getcsv($line, $this->separator) as $value) {
            $key = str_replace([
                'mp_',
                'reference',
            ], [
                '',
                'id',
            ], mb_strtolower($value));
            $keys[] = $key;
        }

        return $keys;
    }
}