<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Xcentric\EntityHydratorBundle\Entity\HydratableEntityInterface;
use Xcentric\EntityHydratorBundle\Service\Entity\Enum;
use Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParserInterface;

/**
 * Class Unattached
 * @package Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser
 */
class Unattached extends AbstractObjectParser
{
    /**
     * @param $rawValue
     * @return mixed|object
     * @throws MissingFqnException
     * @throws \ReflectionException
     */
    public function parse($rawValue)
    {
        $this->figureOutFqn($rawValue);

        if (!$this->fqn) {
            throw new MissingFqnException('Entity fqn is missing');
        }

        if (isset($rawValue[Enum::ENTITY_FQN_FLAG])) {
            $this->handleDataRow($rawValue);
        } else {
            foreach ($rawValue as $dataRow) {
                $this->handleDataRow($dataRow);
            }
        }

        return true;
    }

    /**
     * @param array $dataRow
     * @return null|HydratableEntityInterface
     * @throws \ReflectionException
     */
    protected function handleDataRow(array $dataRow) {
        return !empty($dataRow['id'])
            ? $this->handleUpdatedEmbeddedEntity($dataRow, $this->fqn) :
            $this->handleNewEmbeddedEntity($dataRow, $this->fqn);
    }

    public function setFqn(string $entityFQN): ValueParserInterface
    {
        $this->fqn = $entityFQN;

        return $this;
    }

    protected function figureOutFqn(array $values): Unattached
    {
        if (isset($values[Enum::ENTITY_FQN_FLAG])) {
            $this->fqn = $values[Enum::ENTITY_FQN_FLAG];
        } elseif (isset($values[0][Enum::ENTITY_FQN_FLAG])) {
            $this->fqn = $values[0][Enum::ENTITY_FQN_FLAG];
        }

        return $this;
    }
}