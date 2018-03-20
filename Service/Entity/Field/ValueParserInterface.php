<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field;
use Doctrine\ORM\Mapping\Annotation;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

/**
 * Interface ValueParserInterface
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field
 */
interface ValueParserInterface
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue);

    /**
     * @param string $entityFQN
     * @return ValueParserInterface
     */
    public function setFqn(string $entityFQN): ValueParserInterface;

    /**
     * @param HydratableEntityInterface $entity
     * @return ValueParserInterface
     */
    public function setEntity(HydratableEntityInterface $entity): ValueParserInterface;

    /**
     * @param Annotation $annotation
     * @return ValueParserInterface
     */
    public function setAnnotation(Annotation $annotation): ValueParserInterface;
}