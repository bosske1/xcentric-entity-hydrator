<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity;

use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

interface HydratorInterface
{
    /**
     * @param HydratableEntityInterface $entity
     * @param array $data
     * @return HydratableEntityInterface
     * @throws \ReflectionException
     */
    public function hydrate(HydratableEntityInterface $entity, array $data): HydratableEntityInterface;

    /**
     * @param string $propertyName
     * @return HydratorInterface
     */
    public function addSkipProperty(string $propertyName): HydratorInterface;

    /**
     * @param string $propertyName
     * @return HydratorInterface
     */
    public function addSkipNullProperty(string $propertyName): HydratorInterface;
}