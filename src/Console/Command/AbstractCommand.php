<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Console\Command;

use Gpupo\CommonSdk\Console\Command\AbstractCommand as Core;
use Symfony\Component\Console\Input\InputOption;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractCommand extends Core
{
    const prefix = 'markethub:mercadopago:';

    public function getProjectDataFilename(): string
    {
        return sprintf('var/data/markethub-mercadopago-%d.yaml', $this->getFactory()->getOptions()->get('client_id'));
    }

    protected function addOptionsForList()
    {
        $this
            ->addOption(
                'offset',
                null,
                InputOption::VALUE_REQUIRED,
                'Current offset of list',
                0
            )
            ->addOption(
                'max',
                null,
                InputOption::VALUE_REQUIRED,
                'Max items quantity',
                100
            );
    }
}
