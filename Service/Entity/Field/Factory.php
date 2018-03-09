<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Factory
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field
 */
class Factory implements FactoryInterface
{
    const PARSER_PREFIX = 'xcentric.entity_hydrator.parser.';
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
     * @param array $propertyAnnotations
     * @return ValueParserInterface
     */
    public function spawn(array $propertyAnnotations): ?ValueParserInterface
    {
        $columnAnnotation = $this->findAnnotation($propertyAnnotations, Column::class);

        if ($columnAnnotation) {
            return $this->spawnByColumnAnnotation($columnAnnotation);
        }

        $manyToOneAnnotation = $this->findAnnotation($propertyAnnotations, ManyToOne::class);

        if ($manyToOneAnnotation) {
            return $this->spawnByManyToOne($manyToOneAnnotation);
        }

	    $oneToManyAnnotation = $this->findAnnotation($propertyAnnotations, OneToMany::class);

	    if ($oneToManyAnnotation) {
		    return $this->spawnByOneToMany($oneToManyAnnotation);
	    }

	    return null;
    }

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
     * @param Column $columnAnnotation
     * @return null|ValueParserInterface
     */
    private function spawnByColumnAnnotation(Column $columnAnnotation): ?ValueParserInterface
    {

        $parserClass = self::PARSER_PREFIX . $columnAnnotation->type;

        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->has($parserClass) ? $this->container->get($parserClass)
            : $this->container->get(self::PARSER_PREFIX . 'generic');

        return $valueParser;
    }

    private function spawnByManyToOne(ManyToOne $manyToOneAnnotation): ?ValueParserInterface
    {
        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->get(self::PARSER_PREFIX . 'embedded');
        $valueParser->setFqn($manyToOneAnnotation->targetEntity);

        return $valueParser;
    }

    private function spawnByOneToMany(OneToMany $oneToManyAnnotation): ?ValueParserInterface
    {
        /**
         * @var ValueParserInterface $valueParser
         */
        $valueParser = $this->container->get(self::PARSER_PREFIX . 'embedded');
	    $valueParser->setFqn($oneToManyAnnotation->targetEntity);

	    return $valueParser;
    }
}