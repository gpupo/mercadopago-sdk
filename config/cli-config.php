<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Gpupo\MercadopagoSdk\Tests\Bootstrap;

require 'vendor/autoload.php';

$entityManager = Bootstrap::factoryDoctrineEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
