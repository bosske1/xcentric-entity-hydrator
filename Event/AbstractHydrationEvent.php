<?php

namespace Xcentric\EntityHydratorBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

/**
 * Class AbstractHydrationEvent
 * @package Xcentric\EntityHydratorBundle\Event
 */
class AbstractHydrationEvent extends Event
{
    /**
     * @var HydratableEntityInterface
     */
    protected $entity;

    /**
     * @return HydratableEntityInterface
     */
    public function getEntity(): HydratableEntityInterface
    {
        return $this->entity;
    }

    /**
     * @param HydratableEntityInterface $entity
     * @return AbstractHydrationEvent
     */
    public function setEntity(HydratableEntityInterface $entity): AbstractHydrationEvent
    {
        $this->entity = $entity;
        return $this;
    }
}