<?php

namespace App\Service;

use App\Entity\Picture;

class PictureService
{

  public function __construct()
  {
  }

  public function updatingPicture(Picture $picture)
  {
    if (null !== $picture->getId()) {
      $picture->setName(sprintf('__UPDATING__%s', $picture->getName()));
    }
  }
}
