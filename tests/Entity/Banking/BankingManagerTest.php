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

namespace  Gpupo\MercadopagoSdk\Tests\Entity\Banking;

use Gpupo\Common\Entity\ArrayCollection;
use Gpupo\CommonSchema\ORM\Decorator\Banking\Report\Records;
use Gpupo\CommonSchema\ORM\Entity\Banking\Report\Record;
use Gpupo\CommonSchema\ORM\Entity\Banking\Report\Report;
use Gpupo\MercadopagoSdk\Tests\TestCaseAbstract;
use Symfony\Component\Filesystem\Filesystem;

// use Gpupo\CommonSchema\ArrayCollection\Banking\Report\Report;

/**
 * @coversDefaultClass \Gpupo\MercadopagoSdk\Entity\Banking\BankingManager
 */
class BankingManagerTest extends TestCaseAbstract
{
    public function testGetReportList()
    {
        $manager = $this->mockupManager('mockup/Banking/reports.yaml');
        $list = $manager->getReportList();
        $this->assertInstanceOf(ArrayCollection::class, $list);
    }

    public function testFindReportById()
    {
        $manager = $this->getFactory()->factoryManager('banking');
        $fileSystem = new Filesystem();
        $fileSystem->copy(static::getResourcesPath().'/mockup/Banking/bank-report-123.csv', static::getVarPath().'/cache/bank-report-123.csv');
        $report = $manager->fillReport($this->factoryReport());
        $this->assertInstanceOf(Report::class, $report);
        $this->assertInstanceOf(Record::class, $report->getRecords()->first());
    }

    /**
     * @large
     */
    public function testPersist()
    {
        $manager = $this->getFactory()->factoryManager('banking');

        $report = $manager->fillReport($this->factoryReport());

        $this->assertInstanceOf(Report::class, $report);
        $entityManager = $this->getDoctrineEntityManager();
        $repository = $entityManager->getRepository(Report::class);

        if ($row = $repository->findByFileName('bank-report-123.csv')) {
            $entityManager->remove($row);
            $entityManager->flush();
        }

        $entityManager->persist($report);
        $entityManager->flush();

        $row = $repository->findByFileName('bank-report-123.csv');
        $this->assertInstanceOf(Report::class, $row);
        $this->assertSame('bank-report-123.csv', $row->getFileName());
        $this->assertInstanceOf(Record::class, $row->getRecords()->first());
    }

    /**
     * @large
     */
    public function testFindTradingRecords()
    {
        $repository = $this->getDoctrineEntityManager()->getRepository(Record::class);
        $list = $repository->findByExternalId(1657955112);
        $this->assertNotNull($list);

        $this->assertSame(2, $list->count(), 'Count records');

        foreach ($list as $record) {
            $this->assertInstanceOf(Record::class, $record);
            $this->assertSame(1657955112, $record->getExternalId());
        }

        return $list;
    }

    /**
     * @depends testFindTradingRecords
     * @large
     */
    public function testSumOfRecordsWithMediation(Records $records)
    {
        $this->assertSame(0.0, $records->getTotalOf('gross_amount'), 'gross');
        $this->assertSame(0.0, $records->getTotalGross(), 'gross with alias');
        $this->assertSame(0.0, $records->getTotalOf('fee_amount'), 'fee');
        $this->assertSame(0.0, $records->getTotalFee(), 'fee with alias');

        $this->assertSame(0.0, $records->getTotalOf('financing_fee_amount'), 'financing_fee');
        $this->assertSame(0.0, $records->getTotalOf('shipping_fee_amount'), 'shipping_fee');
        $this->assertSame(0.0, $records->getTotalOf('taxes_amount'), 'taxes');
        $this->assertSame(0.0, $records->getTotalOf('coupon_amount'), 'coupon');

        $this->assertSame(289.32, $records->getTotalOf('net_credit_amount'), 'credit');
        $this->assertSame(289.32, $records->getTotalOf('net_debit_amount'), 'debit');
    }

    protected function factoryReport()
    {
        $report = new Report();
        $report->setFileName('bank-report-123.csv');
        $report->setInstitution('mercadopago');

        return $report;
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
