<?php


namespace App\Utils;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;


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
