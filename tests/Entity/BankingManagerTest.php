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

namespace  Gpupo\MercadopagoSdk\Tests\Entity;

use Gpupo\Common\Entity\Collection;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @coversDefaultClass \Gpupo\MercadopagoSdk\Entity\BankingManager
 */
class BankingManagerTest extends TestCaseAbstract
{
    public function testGetReportList()
    {
        $manager = $this->mockupManager('mockup/Banking/reports.yaml');
        $list = $manager->getReportList();
        $this->assertInstanceOf(Collection::class, $list);
    }

    public function testFindReportById()
    {
        $manager = $this->getFactory()->factoryManager('banking');
        $fileSystem = new Filesystem();
        $fileSystem->copy(static::getResourcesPath().'/mockup/Banking/bank-report-123.csv', static::getVarPath().'/cache/bank-report-123.csv');
        $report = $manager->findReportById('bank-report-123.csv');
        $this->assertInternalType('array', $report);
    }

    protected function mockupManager($file = null)
    {
        $data = $this->getResourceYaml($file);
        $manager = $this->getFactory()->factoryManager('banking');
        $response = $this->factoryResponseFromArray($data);
        $manager->setDryRun($response);

        return $manager;
    }
}
