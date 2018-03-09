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
    private $entityManager;

    /**
     * @var HydratorInterface $hydrator
     */
    private $hydrator;

    /**
     * @var string
     */
    private $fqn;

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

        return !empty($rawValue['id']) ? $this->entityManager->find($this->fqn, $rawValue['id'])
            : $this->insertNewEmbeddedEntity($rawValue, $this->fqn);
    }

    public function setFqn(string $entityFQN): ValueParserInterface
    {
        $this->fqn = $entityFQN;

        return $this;
    }

	/**
	 * @param array $rawValues
	 * @param string $entityFqn
	 *
	 * @return HydratableEntityInterface|array
	 * @throws \ReflectionException
	 */
    private function insertNewEmbeddedEntity(array $rawValues, string $entityFqn)
    {
	    if (empty($rawValues)) {
		    return null;
	    }

	    if (isset($rawValues[0])) {
		    //array of arrays
		    $newarray = array();

		    foreach ($rawValues as $value) {
		    	$newarray[] = $this->insertNewEmbeddedEntity($value, $entityFqn);
		    }

			return $newarray;
	    }

        $newEntity = new $entityFqn();

        $newEntity = $this->hydrator->hydrate($newEntity, $rawValues);

        $this->entityManager->persist($newEntity);

        return $newEntity;
    }

}