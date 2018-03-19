<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

/**
 * Class Boolean
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Boolean extends AbstractValueParser
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue)
    {
        return (bool)$rawValue;
    }
}