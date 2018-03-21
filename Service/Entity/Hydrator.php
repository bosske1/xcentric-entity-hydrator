<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\Field\FactoryInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;


class Hydrator implements HydratorInterface
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var FactoryInterface $valueParserFactory
     */
    private $valueParserFactory;

    /**
     * @var array
     */
    private $entityModifiers = [];

    /**
     *
     * Array of properties to be skipped.
     *
     * @var array
     */
    private $skip = ['id'];

    /**
     *
     * Array of properties to be skipped if value is null.
     *
     * @var array
     */
    private $skipNull = [];

    /**
     * Hydrator constructor.
     * @param EntityManagerInterface $entityManager
     * @param FactoryInterface $factory
     */
    public function __construct(EntityManagerInterface $entityManager, FactoryInterface $factory)
    {
        $this->entityManager = $entityManager;
        $this->valueParserFactory = $factory;
    }

    /**
     * @param HydratableEntityInterface $entity
     * @param array $data
     * @return HydratableEntityInterface
     * @throws \ReflectionException
     */
    public function hydrate(HydratableEntityInterface $entity, array $data): HydratableEntityInterface
    {
        $reflectionClass = new \ReflectionClass($entity);

        $this->runModifiers($entity, $data);

        foreach ($data as $property => $rawValue) {
            if ($this->checkProperty($reflectionClass, $property)) {
                $value = $this->parseValue($reflectionClass, $property, $rawValue, $entity);
                $this->setValue($reflectionClass, $entity, $property, $value);
            } elseif ($this->checkUnattached($property, $rawValue)) {
                $this->parseValue($reflectionClass, $property, $rawValue, $entity);
            }
        }

        return $entity;
    }

    /**
     * @param string $propertyName
     * @return HydratorInterface
     */
    public function addSkipProperty(string $propertyName): HydratorInterface
    {
        if (!in_array($propertyName, $this->skip)) {
            $this->skip[] = $propertyName;
        }

        return $this;
    }

    /**
     * @param string $propertyName
     * @return HydratorInterface
     */
    public function addSkipNullProperty(string $propertyName): HydratorInterface
    {
        if (!in_array($propertyName, $this->skipNull)) {
            $this->skipNull[] = $propertyName;
        }

        return $this;
    }

    /**
     * @param EntityModifierInterface $entityModifier
     * @return HydratorInterface
     */
    public function registerEntityModifier(EntityModifierInterface $entityModifier): HydratorInterface
    {
        $this->entityModifiers[] = $entityModifier;
        return $this;
    }

    private function runModifiers(HydratableEntityInterface $entity, array $data)
    {
        /**
         * @var EntityModifierInterface $entityModifier
         */
        foreach ($this->entityModifiers as $entityModifier) {
            if ($entityModifier->isApplicable($entity, $data)) {
                $entityModifier->modify($entity, $data);
            }
        }
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string $propertyName
     * @return bool
     */
    private function checkProperty(\ReflectionClass $reflectionClass, string $propertyName): bool
    {
        $setterName = 'set' . ucfirst($propertyName);
        if (!$reflectionClass->hasMethod($setterName)) {
            return false;
        }

        return !in_array($propertyName, $this->skip) && !in_array($propertyName, $this->skipNull);
    }

    private function parseValue(\ReflectionClass $reflectionClass, string $propertyName, $rawValue, HydratableEntityInterface $entity)
    {
        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->valueParserFactory->spawn($reflectionClass, $entity, $propertyName);

        if ($valueParser) {
            return $valueParser->parse($rawValue);
        }

        return $rawValue;
    }

    private function setValue(\ReflectionClass $reflectionClass, HydratableEntityInterface $entity, string $propertyName, $value)
    {
        $getterName = 'get' . ucfirst($propertyName);

        if ($reflectionClass->hasMethod($getterName) && $value instanceof Collection) {
            $this->setValueCollection($reflectionClass, $entity, $propertyName, $value);
        } else {
            $this->setValueSimple($entity, $propertyName, $value);
        }
    }

    private function setValueSimple(HydratableEntityInterface $entity, string $propertyName, $value)
    {
        $setterName = 'set' . ucfirst($propertyName);

        $entity->{$setterName}($value);
    }

    private function setValueCollection(\ReflectionClass $reflectionClass, HydratableEntityInterface $entity, string $propertyName, $value)
    {
        $getterName = 'get' . ucfirst($propertyName);

        if ($reflectionClass->hasMethod($getterName)) {
            $collection = $entity->{$getterName}();

            if ($collection instanceof Collection) {
                /**
                 * @var HydratableEntityInterface $item
                 */
                foreach ($value as $item) {
                    $matchingItems = $collection->filter(
                        function ($entry) use ($item) {
                            /**
                             * @var HydratableEntityInterface $entry
                             */
                            return $entry->getId() === $item->getId();
                        }
                    );

                    if ($matchingItems->count() > 0) {
                        foreach ($matchingItems as $matchingItem) {
                            $collection->removeElement($matchingItem);
                        }
                    }

                    $collection->add($item);
                }
            }
        }
    }

    private function checkUnattached(string $propertyName, $values): bool
    {
        if (in_array($propertyName, $this->skip) || in_array($propertyName, $this->skipNull)) {
            return false;
        }

        return is_array($values) && (isset($values[Enum::ENTITY_FQN_FLAG]) || isset($values[0][Enum::ENTITY_FQN_FLAG]));
    }

}