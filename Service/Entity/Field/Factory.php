<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;

/**
 * Class Factory
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field
 */
class Factory implements FactoryInterface
{
    const PARSER_PREFIX = 'xcentric.entity_hydrator.parser.';
    const DATE_FORMAT = 'd.m.Y';

    /**
     * @var ContainerInterface $containerInterface
     */
    private $container;

    /**
     * Factory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param HydratableEntityInterface $entity
     * @param string $propertyName
     * @param $rawValue
     * @return null|ValueParserInterface
     * @throws \ReflectionException
     */
    public function spawn(\ReflectionClass $reflectionClass, HydratableEntityInterface $entity, string $propertyName, $rawValue): ?ValueParserInterface
    {
        $propertyAnnotations = $this->getColumnAnnotations($reflectionClass, $propertyName);

        if (empty($propertyAnnotations)) {
            return $this->resolveUnknown($rawValue, $entity);
        }

        $columnAnnotation = $this->findAnnotation($propertyAnnotations, Column::class);

        if ($columnAnnotation) {
            return $this->spawnByColumnAnnotation($columnAnnotation, $entity);
        }

        $manyToOneAnnotation = self::findAnnotation($propertyAnnotations, ManyToOne::class);
        $manyToManyAnnotation = self::findAnnotation($propertyAnnotations, ManyToMany::class);

        if ($manyToOneAnnotation) {
            return $this->spawnByManyToOne($manyToOneAnnotation, $entity);
        }

        if ($manyToManyAnnotation) {
            // Same shit:
            return $this->spawnByManyToMany($manyToManyAnnotation, $entity);
        }

	    $oneToManyAnnotation = self::findAnnotation($propertyAnnotations, OneToMany::class);

	    if ($oneToManyAnnotation) {
		    return $this->spawnByOneToMany($oneToManyAnnotation, $entity);
	    }

	    return null;
    }

    /**
     * @param array $propertyAnnotations
     * @param string $annotationName
     * @return mixed|null
     * @throws \ReflectionException
     */
    public static function findAnnotation(array $propertyAnnotations, string $annotationName)
    {
        foreach ($propertyAnnotations as $propertyAnnotation) {
            $reflectionClass = new \ReflectionClass($propertyAnnotation);

            if ($reflectionClass->getName() == $annotationName) {
                return $propertyAnnotation;
            }
        }

        return null;
    }

    /**
     * @param $rawValue
     * @param HydratableEntityInterface $entity
     * @return ValueParserInterface
     */
    private function resolveUnknown($rawValue, HydratableEntityInterface $entity): ValueParserInterface
    {
        if (is_array($rawValue)) {
            return $this->spawnUnattached($entity);
        } else if (is_numeric($rawValue)) {
            return $this->container->get(self::PARSER_PREFIX . 'generic');
        } else if (is_bool($rawValue)) {
            return $this->container->get(self::PARSER_PREFIX . 'boolean');
        } else if ($this->guessDate($rawValue) !== false){
            return $this->container->get(self::PARSER_PREFIX . 'datetime');
        }

        return $this->container->get(self::PARSER_PREFIX . 'generic');
    }

    /**
     * @return ValueParserInterface
     */
    private function spawnUnattached(HydratableEntityInterface $entity): ValueParserInterface
    {
        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->get(self::PARSER_PREFIX . 'unattached');
        $valueParser->setEntity($entity);

        return $valueParser;
    }

    /**
     * @param Column $columnAnnotation
     * @return null|ValueParserInterface
     */
    private function spawnByColumnAnnotation(Column $columnAnnotation, HydratableEntityInterface $entity): ?ValueParserInterface
    {

        $parserClass = self::PARSER_PREFIX . $columnAnnotation->type;

        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->has($parserClass) ? $this->container->get($parserClass)
            : $this->container->get(self::PARSER_PREFIX . 'generic');

        $valueParser->setEntity($entity);
        $valueParser->setAnnotation($columnAnnotation);

        return $valueParser;
    }

    /**
     * @param ManyToOne $manyToOneAnnotation
     * @param HydratableEntityInterface $entity
     * @return null|ValueParserInterface
     */
    private function spawnByManyToOne(ManyToOne $manyToOneAnnotation, HydratableEntityInterface $entity): ?ValueParserInterface
    {
        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->get(self::PARSER_PREFIX . 'embedded');
        $valueParser->setFqn($manyToOneAnnotation->targetEntity);
        $valueParser->setEntity($entity);
        $valueParser->setAnnotation($manyToOneAnnotation);

        return $valueParser;
    }

    /**
     * @param OneToMany $oneToManyAnnotation
     * @param HydratableEntityInterface $entity
     * @return null|ValueParserInterface
     */
    private function spawnByOneToMany(OneToMany $oneToManyAnnotation, HydratableEntityInterface $entity): ?ValueParserInterface
    {
        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->get(self::PARSER_PREFIX . 'collection');
	    $valueParser->setFqn($oneToManyAnnotation->targetEntity);
        $valueParser->setEntity($entity);
        $valueParser->setAnnotation($oneToManyAnnotation);

	    return $valueParser;
    }

    /**
     * @param ManyToMany $manyToManyAnnotation
     * @param HydratableEntityInterface $entity
     * @return null|ValueParserInterface
     */
    private function spawnByManyToMany(ManyToMany $manyToManyAnnotation, HydratableEntityInterface $entity): ?ValueParserInterface
    {
        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->get(self::PARSER_PREFIX . 'collection');
	    $valueParser->setFqn($manyToManyAnnotation->targetEntity);
        $valueParser->setEntity($entity);
        $valueParser->setAnnotation($manyToManyAnnotation);

	    return $valueParser;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string $propertyName
     * @return array
     */
    private function getColumnAnnotations(\ReflectionClass $reflectionClass, string $propertyName): array
    {
        try {
            $annotationReader = new AnnotationReader();
            $reflectionProperty = new \ReflectionProperty($reflectionClass->getName(), $propertyName);

            return $annotationReader->getPropertyAnnotations($reflectionProperty);
        } catch (AnnotationException $ae) {
        } catch (\ReflectionException $re) {
        }
        return array();
    }

    private function guessDate($value)
    {
        $date = \DateTime::createFromFormat(self::DATE_FORMAT, $value);
        if ($date === false) {
            $date = \DateTime::createFromFormat(\DateTime::ISO8601, $value);
        }
        return $date;
    }
}