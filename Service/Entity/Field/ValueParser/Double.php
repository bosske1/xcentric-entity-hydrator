<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

/**
 * Class Double
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Double extends AbstractValueParser
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue)
    {
        return ($rawValue !== null) ? (float)$rawValue : null;
    }
}