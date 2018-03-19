<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

/**
 * Class Datetime
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Datetime extends AbstractValueParser
{
    public function parse($rawValue)
    {
        return new \DateTime($rawValue);
    }
}