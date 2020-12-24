<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Tests;

use Gpupo\CommonSdk\Tests\TestCaseAbstract as CommonSdkTestCaseAbstract;
use Gpupo\MercadopagoSdk\Factory;

abstract class TestCaseAbstract extends CommonSdkTestCaseAbstract
{
    private $factory;

    public static function getResourcesPath()
    {
        return \dirname(__DIR__).'/Resources/';
    }

    public static function getVarPath()
    {
        return \dirname(__DIR__).'/var/';
    }

    public function factoryClient()
    {
        return $this->getFactory()->getClient();
    }

    protected function getDoctrineEntityManager()
    {
        return Bootstrap::factoryDoctrineEntityManager();
    }

    protected function getOptions()
    {
        return [
            'client_id' => $this->getConstant('CLIENT_ID'),
            'client_secret' => $this->getConstant('CLIENT_SECRET'),
            'access_token' => $this->getConstant('ACCESS_TOKEN'),
            'refresh_token' => $this->getConstant('REFRESH_TOKEN'),
            'verbose' => $this->getConstant('VERBOSE'),
            'dryrun' => $this->getConstant('DRYRUN'),
            'user_id' => $this->getConstant('USER_ID'),
        ];
    }

    protected function getFactory()
    {
        if (!$this->factory) {
            $this->factory = Factory::getInstance()->setup($this->getOptions(), $this->getLogger());
        }

        return $this->factory;
    }

    /**
     * Requer Implementação mas não será abstrato para não impedir testes que não o usam.
     *
     * @param null|mixed $filename
     */
    protected function getManager($filename = null)
    {
        unset($filename);
    }

    protected function hasToken()
    {
        return $this->hasConstant('SECRET_KEY');
    }
}
