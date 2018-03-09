<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Throwable;

/**
 * Class MissingFqnException
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class MissingFqnException extends \Exception
{
    /**
     * MissingFqnException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}