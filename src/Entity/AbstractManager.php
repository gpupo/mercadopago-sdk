<?php

declare(strict_types=1);

/*
 * This file is part of gpupo/mercadopago-sdk created by Gilmar Pupo <contact@gpupo.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://opensource.gpupo.com/>
 */

namespace Gpupo\MercadopagoSdk\Entity;

use Gpupo\Common\Entity\CollectionInterface;
use Gpupo\CommonSchema\Converters\ConverterContainerTrait;
use Gpupo\CommonSdk\Entity\EntityInterface;
use Gpupo\CommonSdk\Entity\ManagerAbstract;
use Gpupo\CommonSdk\Entity\ManagerInterface;

abstract class AbstractManager extends ManagerAbstract implements ManagerInterface
{
    use ConverterContainerTrait;

    public function factoryMap($operation, array $parameters = null)
    {
        $all = array_merge($this->fetchDefaultParameters(), (array) $parameters);

        return parent::factoryMap($operation, $all);
    }

    public function save(EntityInterface $entity, $route = 'save')
    {
        return $this->execute($this->factoryMap($route), $entity->toJson($route));
    }

    public function findById($itemId): ?CollectionInterface
    {
        $data = parent::findById($itemId);

        if (empty($data) || 404 === $data->get('status')) {
            return null;
        }

        return $this->factoryEntity($data->toArray());
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function update(EntityInterface $entity, EntityInterface $existent = null)
    {
        $text = 'Chamada a Atualização de entity '.$this->entity;

        return $this->log('debug', $text, [
            'entity' => $entity,
            'existent' => $existent,
        ]);
    }

    protected function fetchDefaultParameters(): array
    {
        return (array) $this->getClient()->getOptions()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchPrepare($data): ?CollectionInterface
    {
        if (empty($data)) {
            return null;
        }

        return $this->factoryEntityCollection($data);
    }

    protected function factoryEntityCollection($data): CollectionInterface
    {
        return $this->factoryNeighborObject($this->getEntityName().'Collection', $data);
    }
}
