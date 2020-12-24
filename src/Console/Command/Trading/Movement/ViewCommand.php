<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Console\Command\Trading\Movement;

use Gpupo\MercadopagoSdk\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ViewCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::prefix.'trading:movement:view')
            ->setDescription('Get the Movement detail on MercadoPago')
            ->addArgument('id', InputArgument::REQUIRED, 'MercadoPago Reference Id');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $movementManager = $this->getFactory()->factoryManager('movement');

        try {
            $payment = $movementManager->findPaymentById($id);

            if (!$payment) {
                throw new \Exception('Payment Not Found');
            }

            $this->writeInfo($output, $payment->toArray());
        } catch (\Exception $exception) {
            $output->writeln(sprintf('Error: <bg=red>%s</>', $exception->getmessage()));
        }

        return 0;
    }
}
