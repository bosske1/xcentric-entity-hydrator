<?php

namespace Xcentric\EntityHydratorBundle\Service\Entity\Field\ValueParser;

use Doctrine\Common\Collections\ArrayCollection;

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
        if (!$this->fqn) {
            throw new MissingFqnException('Entity fqn is missing');
        }

        foreach ($rawValue as $dataRow) {
            $createdEmbeddedEntity = !empty($dataRow['id']) ? $this->handleUpdatedEmbeddedEntity($dataRow, $this->fqn)
                : $this->handleNewEmbeddedEntity($dataRow, $this->fqn);
            $objects->add($createdEmbeddedEntity);
        }

        return $objects;
    }
}