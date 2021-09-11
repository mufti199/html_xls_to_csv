<?php declare(strict_types=1);

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;


class AiaXlsFileConverter {
  public function __construct() {
    echo "Calling AiaXlsFileConverter class\n";
  }
  
  /* Converts an xls or html file into a csv.

    .PARAMETER $directory
    The file path in the form of a string. */
  static function convertToCsv (string $directory): string {
    // Initial Validation
    if (! file_exists($directory)) {
      throw new Exception("File does not exist. Please check spelling or path.\n");
    }
    
    if (! preg_match("/.xls|.html/", $directory)) {
      throw new Exception("Invalid file extension. Must be html or xls.\n");
    }
      
    try {
      // If the file is a valid xls file, read it
      $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
      $reader->setReadDataOnly(true);
      $spreadSheet = $reader->load($directory) or throw new Exception("Unable to read file!\n");
      
      // Create a csv file for each sheet and store them into a folder
      $foldername = preg_replace("/\.[^.]+$/", "", $directory);
      $filename = "$foldername.csv";
      $sheetCount = $spreadSheet->getSheetCount();
      if (! is_dir("$foldername")){
        mkdir("$foldername");
      }  
      if ($sheetCount > 1) {
        $sheetNames = $spreadSheet->getSheetNames();
        for ($i = 0; $i < $sheetCount; $i++) {
          $sheetName = $sheetNames[$i];
          $sheet = $spreadSheet->getSheet($i)->toArray();
          $csvFile = fopen("$foldername/$foldername-$sheetName.csv", "w") or throw new Exception("Unable to create csv file!\n");
          self::writeToCSV($csvFile, $sheet);
          fclose($csvFile);
        }
      } else {
        $filename = "$foldername.csv";
        $sheet = $spreadSheet->getSheet(0)->toArray();
        $csvFile = fopen("$filename", "w") or throw new Exception("Unable to create csv file!\n");
        self::writeToCSV($csvFile, $sheet);
        fclose($csvFile);
      }
    } catch (Exception $error) {
      if ($error->getMessage() == "Unable to read file!\n") {
        throw $error;
      } else {
        $tables = self::getHtmlTables($directory);
        $filename = preg_replace("/\.[^.]+$/", ".csv", $directory);
        $file = fopen("$filename", "w");
        foreach($tables as $table) {
          $heads = $table->getElementsByTagName('th');
          $headLength = count($heads);
          $headNum = 1;
          
          $rows = $table->getElementsByTagName('tr'); 
          foreach ($rows as $row) {
            $cols = $row->getElementsByTagName('td');
            $colLength = count($cols);
            $colNum = 1;
            foreach ($cols as $col) {
              $col = $col->nodeValue;
              if ($colNum == $colLength) {
                fwrite($file, "$col\n");
              } else {
                fwrite($file, "$col,");
              }
              $colNum++;
            }
          }
        }
        fclose($file);
      }
    }

    return "$filename\n";
  }

  /* Validation check for html file conversion 

  .PARAMETER $file
  The html file resource, recieved from fopen or similar.
  
  .OUTPUT 
  Returns all the tables of the file as a DOM object. */
  private static function getHtmlTables ($directory) {
    $file = fopen($directory, "r");
    $fileContent = fread($file, filesize($directory));
    // Check the file for HTML tags
    if (! (preg_match("/(<html.+>)/", $fileContent) and preg_match("</html>", $fileContent)
        and strpos($fileContent, "<html") < strpos($fileContent, "</html>"))) {
        throw new Exception("Invalid html file. No HTML tags found.\n");
      }

    // Check the file for TABLE tags
    libxml_use_internal_errors(true);
    $dom = new domDocument;
    $dom->loadHTML($fileContent);
    $tables = $dom->getElementsByTagName('table');
    fclose($file);

    if (count($tables) == 0) {
      throw new Exception("Invalid html file. No TABLE tags found.\n");
    }
    return $fileContent;
  }

  /* Writes data from an array into a csv file

  .PARAMETER $file
  The csv_file resource, recieved from fopen or similar.

  .PARAMETER $sheetData
  A 2D array representing the (row, col) data of the spreedsheet. */
  private static function writeToCSV ($file, $sheetData) {
    
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
}

?>
