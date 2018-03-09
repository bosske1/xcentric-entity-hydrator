<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;

/**
 * Class Integer
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Integer implements ValueParserInterface
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue)
    {
        return (int)$rawValue;
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