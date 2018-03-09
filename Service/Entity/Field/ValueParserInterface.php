<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field;

/**
 * Interface ValueParserInterface
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field
 */
interface ValueParserInterface
{
    /**
     * @param $rawValue
     * @return mixed
     */
    public function parse($rawValue);

    /**
     * @param string $entityFQN
     * @return ValueParserInterface
     */
    public function setFqn(string $entityFQN): ValueParserInterface;
}