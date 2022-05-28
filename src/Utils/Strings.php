<?php


namespace App\Utils;

use Symfony\Component\String\Slugger\AsciiSlugger;

final class Strings
{

  public static function slug(string $String, string $separator = '-'): string
  {
    $slugger = new AsciiSlugger();

    return (string) $slugger->slug($String, $separator);
  }
}
