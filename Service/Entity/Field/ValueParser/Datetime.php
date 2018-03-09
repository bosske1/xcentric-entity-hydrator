<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;

/**
 * Class Datetime
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Datetime implements ValueParserInterface
{
    public function parse($rawValue)
    {
        return new \DateTime($rawValue);
    }

    public function setFqn(string $entityFQN): ValueParserInterface
    {
        return $this;
    }

}