<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Console\Command\Trading\Movement;

use Gpupo\Common\Traits\TableTrait;
use Gpupo\MercadopagoSdk\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @see https://github.com/mercadopago/code-examples/tree/master/movements/search/bash
 */
class ListCommand extends AbstractCommand
{
    use TableTrait;

    private $limit = 50;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::prefix.'trading:movement:list')->setDescription('Get the Movement list on MercadoPago');
        $this->addOptionsForList();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $movementManager = $this->getFactory()->factoryManager('movement');
        $offset = $input->getOption('offset');
        $max = $input->getOption('max');
        $output->writeln(sprintf('Max items from this fetch is <fg=blue> %d </>', $max));

        try {
            $output->writeln(sprintf('Fetching from %d to %d', $offset, ($offset + $this->limit)));
            $response = $movementManager->search('income');
            $paging = $response->get('paging');
            $total = $paging['total'];
            $output->writeln(sprintf('Total: <bg=green;fg=black> %d </>', $total));
            $results = $response->get('results');
            $this->displayTableResults($output, $results);
            // file_put_contents('var/cache/search-raw.yaml', Yaml::dump($response->toArray(), 4, 4));
            // file_put_contents('var/cache/search-results.yaml', Yaml::dump($results));
        } catch (\Exception $exception) {
            $output->writeln(sprintf('Error: <bg=red>%s</>', $exception->getmessage()));
        }

        return 0;
    }
}
