<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and returns the array with the data.
   */
  public function scrap(\DOMDocument $dom): array {
    $papers = array();
    $links = array();
    $a = $dom->getElementsByTagName('a');
    $ids = array();
    $titles = array();
    foreach ($a as $link){
      $l = $link->getAttribute('href');
      if (str_starts_with($l, 'https://proceedings.science/proceeding')) {
        $links[] = $l;
        $div = $link->getElementsByTagName('div')->item(1)->getElementsByTagName('div')->item(1)->getElementsByTagName('div')->item(1);
        $ids[] = $div->nodeValue;
      }      
    }
    for ($j=0; $j < 3; $j++) { 
      $ii = array();
      $au = array();
      $dom->loadHTMLFile($links[$j]);
      $element = $dom->getElementById('block-papertitleblockdewey');
      $titles[] = $element->getElementsByTagName('h2')->item(0)->textContent;
      $type = $dom->getElementsByTagName('ul')->item(3)->getElementsByTagName('li')->item(0)->getElementsByTagName('strong')->item(0)->textContent;
      $institutions = $dom->getElementsByTagName('ul')->item(5)->getElementsByTagName('li');
      $w = $institutions->count();
      for ($i=0; $i < $w; $i++) { 
        $ii[] = $institutions->item($i)->textContent;
      }
      $authors = $dom->getElementsByTagName('ul')->item(4)->getElementsByTagName('li');
      foreach ($authors as $author) {
        $au[] = new Person($author->getElementsByTagName('abbr')->item(0)->textContent, $author->getElementsByTagName('sup')->item(0)->textContent);
      }
      foreach ($au as $author) {
        $iiLength = sizeof($ii);
        for ($i=0; $i < $iiLength; $i++) { 
          if($author->institution == mb_substr($ii[$i], 0, 1)){
            $length = strlen($ii[$i]);
            $author->institution = mb_substr($ii[$i], 2, ($length -2));
          }
        }
      }
      $papers[] = new Paper(
        $ids[$j],
        $titles[$j],
        $type,
        $au
        );
    }

    return $papers;
  }

}
