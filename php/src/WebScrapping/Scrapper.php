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
    $a = $dom->getElementsByTagName('a');
    $ids = array();
    foreach ($a as $link){
      $l = $link->getAttribute('href');
      if (str_starts_with($l, 'https://proceedings.science/proceeding')) {
        $authors = array();
        $au = $link->getElementsByTagName('div')->item(0)->getElementsByTagName('span');
        foreach ($au as $author){
          $authors[] = new Person($author->textContent, $author->getAttribute('title'));
        }
        $id = $link->getElementsByTagName('div')->item(1)->getElementsByTagName('div')->item(1)->getElementsByTagName('div')->item(1)->nodeValue;
        $type = $link->getElementsByTagName('div')->item(1)->getElementsByTagName('div')->item(0)->nodeValue;
        $title = $link->getElementsByTagName('h4')->item(0)->textContent;
        $papers[] = new Paper(
          $id,
          $title,
          $type,
          $authors
          );
      }      
    }

    return $papers;
  }

}
