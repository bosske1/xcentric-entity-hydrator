<?php
/**
 * Created by PhpStorm.
 * User: bosske1
 * Date: 10.3.18.
 * Time: 23.25
 */

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\HydratorInterface;

class Collection implements ValueParserInterface
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
        $objects = new ArrayCollection();
        if (!$this->fqn) {
            throw new MissingFqnException('Entity fqn is missing');
        }

        foreach ($rawValue as $dataRow) {
            $createdEmbeddedEntity = !empty($dataRow['id']) ? $this->entityManager->find($this->fqn, $dataRow['id'])
                : $this->insertNewEmbeddedEntity($dataRow, $this->fqn);
            $objects->add($createdEmbeddedEntity);
        }

        return $objects;
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