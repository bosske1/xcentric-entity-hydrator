<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

/**
 * Class Integer
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Integer extends AbstractValueParser
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue)
    {
        return ($rawValue !== null) ? (int)$rawValue : null;
    }
}