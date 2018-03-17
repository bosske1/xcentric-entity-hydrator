<?php

namespace Xcentric\EntityHydratorBundle\Entity;

/**
 * Interface HydratableEntityInterface
 * @package Xcentric\EntityHydratorBundle\Entity
 */
interface HydratableEntityInterface
{
    /**
     * @return int
     */
    public function getId();

    public function isChanged(): bool;
}