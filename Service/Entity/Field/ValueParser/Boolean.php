<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;


/**
 * Class Boolean
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Boolean implements ValueParserInterface
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue)
    {
        return (bool)$rawValue;
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