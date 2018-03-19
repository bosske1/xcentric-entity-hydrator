<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

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
     * @param string $entityFQN
     * @return ValueParserInterface
     */
    public function setFqn(string $entityFQN): ValueParserInterface
    {
        $this->fqn = $entityFQN;

        return $this;
    }
}