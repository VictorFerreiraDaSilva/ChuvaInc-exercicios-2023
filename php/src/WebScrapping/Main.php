<?php

namespace Chuva\Php\WebScrapping;
require_once __DIR__ . '/../../vendor/box/spout/src/Spout/Autoloader/autoload.php';


use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
/**
 * Runner for the Webscrapping exercice.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    $dom = new \DOMDocument('1.0', 'utf-8');
    $dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');
    
    $data = (new Scrapper())->scrap($dom);
    $highestAuthorCount = 0;
    foreach ($data as $paper) {
      $authorsCount = count($paper->authors);
      if ($authorsCount > $highestAuthorCount) {
        $highestAuthorCount = $authorsCount;
      }
    }
    if ($highestAuthorCount < 9) {
      $highestAuthorCount = 9;
    }
    // Write your logic to save the output file bellow.
    //print_r($highestAuthorCount);
    $cells = array();
    $cells[] = WriterEntityFactory::createCell('ID');
    $cells[] = WriterEntityFactory::createCell('Title');
    $cells[] = WriterEntityFactory::createCell('Type');
    for ($i=1; $i <= $highestAuthorCount; $i++) { 
      $cells[] = WriterEntityFactory::createCell('Author ' . $i);
      $cells[] = WriterEntityFactory::createCell('Author ' . $i . ' Institution');
    }

    $writer = WriterEntityFactory::createXLSXWriter();
    
    $writer->openToFile('assets/output.xlsx');
    $border = (new BorderBuilder())
    ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
    ->build();
    
    $styleHeader = (new StyleBuilder())
           ->setFontBold()
           ->setFontColor(Color::BLACK)
           ->setBorder($border)
           ->build();
    
    $headerRow = WriterEntityFactory::createRow($cells, $styleHeader);
    $rows = array();
    $rows[] = $headerRow;
    foreach ($data as $paper) {
      $clls = array();
      $clls[] = WriterEntityFactory::createCell(strval($paper->id));
      $clls[] = WriterEntityFactory::createCell($paper->title);
      $clls[] = WriterEntityFactory::createCell($paper->type);
      foreach ($paper->authors as $author) {
        $clls[] = WriterEntityFactory::createCell($author->name);
        $clls[] = WriterEntityFactory::createCell($author->institution);
      }
      $row = WriterEntityFactory::createRow($clls);
      $rows[] = $row;
    }
    $writer->addRows($rows);
    $writer->close();
    
  }

}
