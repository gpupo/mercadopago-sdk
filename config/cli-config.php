<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Gpupo\MercadopagoSdk\Tests\Bootstrap;

require 'vendor/autoload.php';

$entityManager = Bootstrap::factoryDoctrineEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
