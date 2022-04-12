<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace  Gpupo\MercadopagoSdk\Tests\Entity;

use Gpupo\Common\Entity\ArrayCollection;
use Gpupo\Common\Entity\Collection;
use Gpupo\CommonSchema\ORM\Entity\Banking\Movement\Movement;
use Gpupo\CommonSchema\ORM\Entity\Banking\Report\Report;
use Gpupo\CommonSchema\ORM\Entity\Trading\Order\Shipping\Payment\Payment;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;
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

    public function testFindPaymentById()
    {
        $manager = $this->mockupManager('mockup/Movement/payment.yaml');
        $payment = $manager->findPaymentById(775046323112);
        $raw = $payment->getExpands();
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame(775046323112, $payment->getPaymentNumber(), 'Payment Number');
        $this->assertSame($raw['transaction_details']['net_received_amount'], $payment->getTransactionNetAmount(), 'Detail net');
        $this->assertSame($raw['transaction_details']['total_paid_amount'], $payment->getTotalPaidAmount(), 'Detail paid');
        $this->assertSame('BRL', $payment->getCurrencyId(), 'currency');
        $this->assertSame(0.0, $payment->getOverpaidAmount());
        // file_put_contents('var/cache/payment.yaml', Yaml::dump($payment->toArray(), 4, 4));
    }

    public function testGetReportList()
    {
        $manager = $this->mockupManager('mockup/Movement/reports.yaml');
        $list = $manager->getReportList();
        $this->assertInstanceOf(ArrayCollection::class, $list);
        $this->assertContainsOnlyInstanceOf(Report::class, $list);
    }

    public function testFillReport()
    {
        $manager = $this->mockupManager('mockup/Movement/report.yaml');
        $fake_report = new Report();
        $fake_report->setFileName('foo.csv');

        $updated_report = $manager->fillReport($fake_report);
        $this->assertInstanceOf(Report::class, $updated_report);
        $movements = $updated_report->getMovements();
        $this->assertInstanceOf(ArrayCollection::class, $movements);
        $this->assertinstanceOf(Movement::class, $movements->first());
    }

    protected function mockupManager($file)
    {
        $data = $this->getResourceYaml($file);
        $manager = $this->getFactory()->factoryManager('movement');
        $response = $this->factoryResponseFromArray($data);
        $manager->setDryRun($response);

        return $manager;
    }
}
