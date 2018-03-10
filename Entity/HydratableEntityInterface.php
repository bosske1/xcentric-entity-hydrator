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
    public function getId(): ?int;

    public function isChanged(): bool;
}