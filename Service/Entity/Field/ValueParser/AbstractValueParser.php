<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Doctrine\ORM\Mapping\Annotation;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;

/**
 * Class AbstractValueParser
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
abstract class AbstractValueParser implements ValueParserInterface
{
    /**
     * @var string
     */
    protected $fqn;

    /**
     * @var HydratableEntityInterface
     */
    protected $entity;

    /**
     * @var Annotation
     */
    protected $annotation;

    /**
     * @param string $entityFQN
     * @return ValueParserInterface
     */
    public function setFqn(string $entityFQN): ValueParserInterface
    {
        $this->fqn = $entityFQN;

        return $this;
    }

    /**
     * @param HydratableEntityInterface $entity
     * @return ValueParserInterface
     */
    public function setEntity(HydratableEntityInterface $entity): ValueParserInterface
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @param Annotation $annotation
     * @return ValueParserInterface
     */
    public function setAnnotation(Annotation $annotation): ValueParserInterface
    {
        $this->annotation = $annotation;
        return $this;
    }

}