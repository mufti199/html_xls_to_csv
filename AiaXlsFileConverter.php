<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;


class AiaXlsFileConverter {

  /* Converts an XLS or HTML file into a csv.

  .PARAMETER $directory
  The file path in the form of a string. 
  
  .OUTPUT $filename | $foldername (only in the case of multiple spreadsheets)
  The name of the created csv file/folder. */
  static function convertToCsv ($directory) {
    // Initial Validation
    if (! file_exists($directory)) {
      throw new Exception("File does not exist. Please check spelling or path.\n");
    }
    if (! preg_match("/.xls|.html/", $directory)) {
      throw new Exception("Invalid file extension. Must be html or xls.\n");
    }
    
    // Try to convert the file as an xls file 
    $regexPrefix = "/(\.)\//";
    $regexSuffix = "/\.[^.]+$/";
    try {
      // If the file is a valid xls file, read it
      $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
      $reader->setReadDataOnly(true);
      $spreadSheet = $reader->load($directory) or throw new Exception("Unable to read file!\n");
      
      // Create a csv file per spreadsheet. For multiple files, store them into a folder
      $foldername = preg_replace($regexSuffix, "", $directory);
      $sheetCount = $spreadSheet->getSheetCount();
      
      // For multiple spreadsheets 
      if ($sheetCount > 1) {
        if (! is_dir("$foldername")){
          mkdir("$foldername");
        }  
        $sheetNames = $spreadSheet->getSheetNames();
        for ($i = 0; $i < $sheetCount; $i++) {
          $sheetName = $sheetNames[$i];
          $sheet = $spreadSheet->getSheet($i)->toArray();
          $csvFile = fopen("$foldername/$foldername-$sheetName.csv", "w") or throw new Exception("Unable to create csv file!\n");
          self::writeXlsToCSV($csvFile, $sheet);
          fclose($csvFile);
          $foldername = preg_replace($regexPrefix, "",$foldername);
        }
        return "(folder) $foldername\n";
      } 
      // For single spreadsheet
      else {
        $filename = "$foldername.csv";
        $sheet = $spreadSheet->getSheet(0)->toArray();
        $csvFile = fopen("$filename", "w") or throw new Exception("Unable to create csv file!\n");
        self::writeXlsToCSV($csvFile, $sheet);
        fclose($csvFile);
        $filename = preg_replace($regexPrefix, "",$filename);
      }
    } 
    // Try to convert the file as an html file
    catch (Exception $error) {
      if ($error->getMessage() == "Unable to read file!\n" or $error->getMessage() == "Unable to create csv file!\n") {
        throw $error;
      } else {
        $filename = preg_replace($regexSuffix, ".csv", $directory);
        $file = fopen("$filename", "w");
        $dom = self::getHtmlDom($directory);
        self::writeHtmlToCsv($file, $dom);
        fclose($file);
        $filename = preg_replace($regexPrefix, "",$filename);
      }
    }

    return "$filename\n";
  }


  /* Performs a validation check for HTML file conversion.
     Returns an HTML DOM if successful.

  .PARAMETER $directory
  The directory (path) of the HTML file
  
  .OUTPUT $dom
  The DOM object of the HTML file. */
  private static function getHtmlDom ($directory) {
    $file = fopen($directory, "r");
    $fileContent = fread($file, filesize($directory));
    fclose($file);
    // Check the file for HTML tags (loadHTML adds the tags even if they aren't in the file)
    if (! (preg_match("/(<html.+>)/", $fileContent) and preg_match("</html>", $fileContent)
    and strpos($fileContent, "<html") < strpos($fileContent, "</html>"))) {
      throw new Exception("Invalid html file. No HTML tags found.\n");
    }
    // Create a DOM document and load the HTML file
    libxml_use_internal_errors(true);
    $dom = new domDocument;
    $dom->loadHTML($fileContent);
    $dom->preserveWhiteSpace = false;
    
    // Check the file for TABLE tags
    $tables = $dom->getElementsByTagName('table');
    $tableCount = count($tables);
    if ($tableCount == 0) {
      throw new Exception("Invalid html file. No TABLE tags found.\n");
    }
    // print_r($tableArray);
    return $dom;
  }


  /* Writes data from an array into a csv file

  .PARAMETER $file
  The csv_file resource, recieved from fopen or similar.

  .PARAMETER $sheetData
  A 2D array representing the (row, col) data of the spreedsheet. */
  private static function writeXlsToCSV ($file, $sheetData) {
    foreach ($sheetData as $row) {
      $rowLength = count($row);
      $colNum = 1;
      foreach ($row as $col) {
        if ($colNum == $rowLength) {
          fwrite($file, "$col\n");
        } else {
          fwrite($file, "$col,");
        }
        $colNum++;
      }
    }
  }


  /* Writes data from an HTML dom into a csv file

  .PARAMETER $file
  The csv_file resource, recieved from fopen or similar.

  .PARAMETER $dom
  An HTML dom */
  private static function writeHtmlToCsv ($file, $dom) {
    $rows = $dom->getElementsByTagName('tr');
    foreach ($rows as $row) {
      if ($row->getElementsByTagName('table')->item(0)){
        continue;
      }
      if ($row->getElementsByTagName('th')->item(0)) {
        foreach($row->getElementsByTagName('th') as $heading) {
          fwrite($file, "\"$heading->textContent\",");
        }
        fwrite($file, "\n");
      } 
      else {
        $cols = $row->getElementsByTagName('td');
        $colLength = count($cols);
        $colNum = 1;
        foreach ($cols as $col) {
          $col = trim($col->textContent);
          if ($colNum == $colLength) {
            fwrite($file, "\"$col\"\n");
          } else {
            fwrite($file, "\"$col\",");
          }
          $colNum++;
        }
      }
    }
  }

}

?>
