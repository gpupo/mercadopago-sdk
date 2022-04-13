<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace  Gpupo\MercadopagoSdk\Tests\Entity;

use Doctrine\Common\Collections\Collection as DCollection;
use Gpupo\Common\Entity\ArrayCollection;
use Gpupo\Common\Entity\Collection;
use Gpupo\CommonSchema\ORM\Entity\Banking\Movement\Movement;
use Gpupo\CommonSchema\ORM\Entity\Banking\Movement\Report;
use Gpupo\CommonSchema\ORM\Entity\Trading\Order\Shipping\Payment\Payment;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @coversDefaultClass \Gpupo\MercadopagoSdk\Entity\MovementManager
 */
class MovementManagerTest extends TestCaseAbstract
{
    public function testGetBalance()
    {
        $manager = $this->mockupManager('mockup/Movement/balance.yaml');
        $balance = $manager->getBalance();
        $this->assertInstanceOf(Collection::class, $balance);
        $this->assertSame(4350.87, $balance->getAvailableBalance());
        $this->assertSame(7987.58, $balance->getUnavailableBalance());
        $this->assertSame(12338.45, $balance->getTotalAmount());
    }

    public function testGetMovementList()
    {
        $manager = $this->mockupManager('mockup/Movement/search-income.yaml');
        $arrayCollection = $manager->getMovementList();
        $this->assertInstanceOf(ArrayCollection::class, $arrayCollection);

        $movement = $arrayCollection->first();
        $this->assertInstanceOf(Movement::class, $movement);
    }

    public function testMovementListEmptyResult()
    {
        $manager = $this->mockupManager('mockup/Movement/search-income-empty.yaml');
        $arrayCollection = $manager->getMovementList();
        $this->assertInstanceOf(ArrayCollection::class, $arrayCollection);
    }

    public function testGetReportList()
    {
        $manager = $this->mockupManager('mockup/Movement/reports.yaml');
        $list = $manager->getReportList();
        $this->assertInstanceOf(ArrayCollection::class, $list);
        $this->assertContainsOnlyInstancesOf(Report::class, $list);
    }

    public function testFillReport()
    {
        // $manager = $this->mockupCsvManager('mockup/Movement/report.csv');
        $manager = $this->getFactory()->factoryManager('movement');
        $fake_report = new Report();
        $fake_report->setFileName('foo.csv');
        $fake_report->setInstitution('mercadopago');

        $file_system = new Filesystem();
        $file_system->copy(static::getResourcesPath().'/mockup/Movement/report.csv', static::getVarPath().'/cache/foo.csv');

        $updated_report = $manager->fillReport($fake_report);
        $this->assertInstanceOf(Report::class, $updated_report);
        $movements = $updated_report->getMovements();
        $this->assertInstanceOf(DCollection::class, $movements);
        $this->assertInstanceOf(Movement::class, $movements->first());
        $this->assertSame($updated_report, $movements->first()->getReport());
    }

    protected function mockupManager($file)
    {
        $data = $this->getResourceYaml($file);
        $manager = $this->getFactory()->factoryManager('movement');
        $response = $this->factoryResponseFromArray($data);
        $manager->setDryRun($response);

        return $manager;
    }

    protected function mockupCsvManager($file)
    {
        $manager = $this->getFactory()->factoryManager('movement');
        $response = $this->factoryResponseFromFixture($file);
        $manager->setDryRun($response);

        return $manager;
    }
}
