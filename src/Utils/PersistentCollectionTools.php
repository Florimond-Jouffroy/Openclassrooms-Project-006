<?php

namespace App\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

final class PersistentCollectionTools
{
    public static function initializeCollection(PersistentCollection $collection): ArrayCollection
    {
        $newCollection = new ArrayCollection();
        foreach ($collection as $element) {
            $newCollection->add($element);
        }

        return $newCollection;
    }
}
