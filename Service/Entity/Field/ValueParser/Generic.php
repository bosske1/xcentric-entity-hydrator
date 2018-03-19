<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

/**
 * Class Generic
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Generic extends AbstractValueParser
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue)
    {
        return $rawValue;
    }
}