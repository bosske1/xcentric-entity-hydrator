<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;

/**
 * Class Double
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Double implements ValueParserInterface
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue)
    {
        return (float)$rawValue;
    }

    /**
     * @param string $entityFQN
     * @return ValueParserInterface
     */
    public function setFqn(string $entityFQN): ValueParserInterface
    {
        return $this;
    }
}