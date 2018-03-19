<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Doctrine\ORM\EntityManagerInterface;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\Enum;
use Xcentric\EntityHydratorBundle\Service\Entity\HydratorInterface;

abstract class AbstractObjectParser extends AbstractValueParser
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * @var HydratorInterface $hydrator
     */
    protected $hydrator;

    public function __construct(EntityManagerInterface $entityManager, HydratorInterface $hydrator)
    {
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
    }

    /**
     * @param array $rawValues
     * @param string $entityFqn
     * @return HydratableEntityInterface
     * @throws \ReflectionException
     */
    protected function handleUpdatedEmbeddedEntity(?array $rawValues, string $entityFqn): ?HydratableEntityInterface
    {
        /**
         * @var HydratableEntityInterface $updatedEntity
         */
        $updatedEntity = $this->entityManager->find($entityFqn, $rawValues['id']);

        if ($this->entityHasChanges($rawValues)) {
            $this->hydrator->hydrate($updatedEntity, $rawValues);
            $this->entityManager->persist($updatedEntity);
        }

        return $updatedEntity;
    }

    /**
     * @param array $rawValues
     * @param string $entityFqn
     * @return HydratableEntityInterface
     * @throws \ReflectionException
     */
    protected function handleNewEmbeddedEntity(?array $rawValues, string $entityFqn): ?HydratableEntityInterface
    {
        if (empty($rawValues)) {
            return null;
        }

        $newEntity = new $entityFqn();

        $newEntity = $this->hydrator->hydrate($newEntity, $rawValues);

        $this->entityManager->persist($newEntity);

        return $newEntity;
    }

    protected function entityHasChanges(?array $rawValues): bool
    {
        return (isset($rawValues[Enum::ENTITY_CHANGED_FLAG]) && $rawValues[Enum::ENTITY_CHANGED_FLAG])
            || (isset($rawValues[Enum::ENTITY_EMBEDDED_CHANGED_FLAG]) && $rawValues[Enum::ENTITY_EMBEDDED_CHANGED_FLAG]);
    }
}