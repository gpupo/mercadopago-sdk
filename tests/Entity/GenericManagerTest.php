<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Tests\Entity;

use Gpupo\CommonSdk\Map;
use Gpupo\MercadopagoSdk\Entity\GenericManager;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;

/**
 * @coversDefaultClass \Gpupo\MercadopagoSdk\Entity\GenericManager
 */
class GenericManagerTest extends TestCaseAbstract
{
    public function testFactoryMap()
    {
        $manager = $this->getFactory()->factoryManager('movement');
        $this->assertInstanceOf(GenericManager::class, $manager);

        $route = [
            'GET',
            $manager::SEARCH_FUNCTION_ENDPOINT.'range={range}&begin_date={begin_date}&end_date={end_date}&offset={offset}&limit={limit}',
        ];

        $days_ago = 7;

        $parameters = [
            'range' => 'date_created',
            'begin_date' => sprintf('NOW-%dDAY', $days_ago),
            'end_date' => 'NOW',
        ];

        $map = $manager->factorySimpleMap($route, $parameters);
        $this->assertInstanceOf(Map::class, $map);
        $this->assertSame('/mercadopago_account/movements/search?range=date_created&begin_date=NOW-7DAY&end_date=NOW&offset=0&limit=30', $map->getResource());
    }
}
