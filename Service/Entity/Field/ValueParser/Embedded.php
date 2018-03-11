<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Doctrine\ORM\EntityManagerInterface;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\HydratorInterface;

/**
 * Class Embedded
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Embedded implements ValueParserInterface
{

    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * @var HydratorInterface $hydrator
     */
    protected $hydrator;

    /**
     * @var string
     */
    protected $fqn;

    public function __construct(EntityManagerInterface $entityManager, HydratorInterface $hydrator)
    {
        $this->entityManager = $entityManager;
        $this->hydrator = $hydrator;
    }

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

        return !empty($rawValue['id']) ? $this->handleUpdatedEmbeddedEntity($rawValue, $this->fqn)
            : $this->handleNewEmbeddedEntity($rawValue, $this->fqn);
    }

    public function setFqn(string $entityFQN): ValueParserInterface
    {
        $this->fqn = $entityFQN;

        return $this;
    }

    /**
     * @param array $rawValues
     * @param string $entityFqn
     * @return HydratableEntityInterface
     * @throws \ReflectionException
     */
    protected function handleUpdatedEmbeddedEntity(array $rawValues, string $entityFqn): ?HydratableEntityInterface
    {
        /**
         * @var HydratableEntityInterface $updatedEntity
         */
        $updatedEntity = $this->entityManager->find($entityFqn, $rawValues['id']);

        if ($rawValues['isChanged']) {
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
    protected function handleNewEmbeddedEntity(array $rawValues, string $entityFqn): ?HydratableEntityInterface
    {
	    if (empty($rawValues)) {
		    return null;
	    }

        $newEntity = new $entityFqn();

        $newEntity = $this->hydrator->hydrate($newEntity, $rawValues);

        $this->entityManager->persist($newEntity);

        return $newEntity;
    }

}