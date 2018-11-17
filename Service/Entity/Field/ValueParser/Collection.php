<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

class Collection extends Embedded
{
    /**
     * @param $rawValue
     * @return mixed|object
     * @throws MissingFqnException
     * @throws \ReflectionException
     */
    public function parse($rawValue)
    {
        $objects = new ArrayCollection();

        if (!empty($rawValue)) {
            foreach ($rawValue as $dataRow) {
                $createdEmbeddedEntity = is_numeric($dataRow) ? $this->handleUnchanged($dataRow, $this->getEntityFqn($dataRow))
                    : (!empty($dataRow['id']) ? $this->handleUpdatedEmbeddedEntity($dataRow, $this->getEntityFqn($dataRow))
                        : $this->handleNewEmbeddedEntity($dataRow, $this->getEntityFqn($dataRow)));

                if ($createdEmbeddedEntity) {
                    $this->setOneToManyEntity($createdEmbeddedEntity);
                    $this->entityManager->persist($createdEmbeddedEntity);
                    $objects->add($createdEmbeddedEntity);
                }
            }
        }

        return $objects;
    }

    protected function setOneToManyEntity(HydratableEntityInterface $hydratableEntity)
    {
        if (!$this->annotation || !$this->annotation instanceof OneToMany) {
            return;
        }
        $propertyName = $this->annotation->mappedBy;

        $setterMethod = 'set' . ucfirst($propertyName);

        if ($propertyName && method_exists($hydratableEntity, $setterMethod)) {
            $hydratableEntity->{$setterMethod}($this->entity);
        }
    }

    protected function getEntityFqn($dataRow): string
    {
        return (isset($dataRow['fqn'])) ? $dataRow['fqn'] : $this->fqn;
    }
}