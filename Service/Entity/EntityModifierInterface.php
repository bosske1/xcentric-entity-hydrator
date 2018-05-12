<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity;

use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

/**
 * Interface EntityModifierInterface
 * @package Xcentric\EntityHydratorBundle\Service\Entity
 */
interface EntityModifierInterface
{
    /**
     * @param HydratableEntityInterface $entity
     * @param array $data
     * @return array
     */
    public function modify(HydratableEntityInterface $entity, array $data);

    /**
     * @param HydratableEntityInterface $entity
     * @param array $data
     * @return bool
     */
    public function isApplicable(HydratableEntityInterface $entity, array $data): bool;

    /**
     * @return string
     */
    public function getModifierType(): string;
}