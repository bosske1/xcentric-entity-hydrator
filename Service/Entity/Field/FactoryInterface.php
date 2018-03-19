<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field;

/**
 * Interface FactoryInterface
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field
 */
interface FactoryInterface
{
    /**
     * @param array $propertyAnnotations
     * @return ValueParserInterface
     */
    public function spawn(?array $propertyAnnotations): ?ValueParserInterface;
}