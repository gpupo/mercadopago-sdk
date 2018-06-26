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

namespace Gpupo\MercadopagoSdk\Console\Command\Banking;

use Gpupo\Common\Traits\TableTrait;
use Gpupo\MercadopagoSdk\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ViewCommand extends AbstractCommand
{
    use TableTrait;

    private $limit = 10;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::prefix.'banking:report:view')
            ->setDescription('Get the Report details.')
            ->addArgument('filename', InputArgument::REQUIRED, 'Report filename');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $manager = $this->getFactory()->factoryManager('banking');

        try {
            $report = $manager->findReportById($filename);

            if (!$report) {
                throw new \Exception('Report Not Found');
            }

            $this->displayTableResults($output, $report, [], 10);
            file_put_contents('var/cache/bank-report.yaml', Yaml::dump($report, 4, 4));
        } catch (\Exception $exception) {
            $output->writeln(sprintf('Error: <bg=red>%s</>', $exception->getmessage()));
        }
    }
}
