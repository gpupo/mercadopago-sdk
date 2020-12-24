<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Console\Command\Trading\Movement;

use Gpupo\MercadopagoSdk\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BalanceCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::prefix.'trading:movement:balance')->setDescription('Get the balance amount');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $movementManager = $this->getFactory()->factoryManager('movement');

        try {
            $response = $movementManager->getBalance();

            $currency = '$';
            if ('BRL' === $response->getCurrencyId()) {
                $currency = 'R$';
            }
            $output->writeln(sprintf('Available: %s<bg=green;fg=black>%s </>', $currency, $response->getAvailableBalance()));
            $output->writeln(sprintf('Unavailable: %s<bg=yellow;fg=black>%s </>', $currency, $response->getUnavailableBalance()));
            $output->writeln(sprintf('Total: %s<bg=green;fg=black>%s </>', $currency, $response->getTotalAmount()));
        } catch (\Exception $exception) {
            $output->writeln(sprintf('Error: <bg=red>%s</>', $exception->getmessage()));
        }

        return 0;
    }
}
