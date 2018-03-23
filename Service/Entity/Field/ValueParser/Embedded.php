<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

/**
 * Class Embedded
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Embedded extends AbstractObjectParser
{

    /**
     * @param $rawValue
     * @return mixed|object
     * @throws MissingFqnException
     * @throws \ReflectionException
     */
    public function parse($rawValue)
    {
        if (!$this->fqn) {
            throw new MissingFqnException('Entity fqn is missing');
        }

        if (empty($rawValue)) {
            return null;
        }

        return (is_numeric($rawValue))
            ? $this->handleUnchanged($rawValue, $this->fqn) :
            (!empty($rawValue['id'])
                ? $this->handleUpdatedEmbeddedEntity($rawValue, $this->fqn) :
                $this->handleNewEmbeddedEntity($rawValue, $this->fqn));
    }

    protected function handleUnchanged(int $id, string $entityFqn): ?HydratableEntityInterface
    {
        /**
         * @var HydratableEntityInterface $entity
         */
        $entity = $this->entityManager->find($entityFqn, $id);

        return $entity;
    }
}