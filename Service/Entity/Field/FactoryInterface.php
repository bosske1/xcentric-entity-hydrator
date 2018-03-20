<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

/**
 * Interface FactoryInterface
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field
 */
interface FactoryInterface
{
    /**
     * @param \ReflectionClass $reflectionClass
     * @param HydratableEntityInterface $entity
     * @param string $propertyName
     * @return null|ValueParserInterface
     */
    public function spawn(\ReflectionClass $reflectionClass, HydratableEntityInterface $entity, string $propertyName): ?ValueParserInterface;
}