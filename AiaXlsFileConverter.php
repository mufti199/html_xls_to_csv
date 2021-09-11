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
        $fileContent = self::getHtmlTables($directory);
        $filename = preg_replace("/\.[^.]+$/", ".csv", $directory);
        $file = fopen("$filename", "w");

      /*start after TABLE tag
        do not write <text> or </text> or whitespace or \n
        th -> heading -> ,
        td -> column -> ,
        tr -> row -> \n
        */
        // Skip lines of the file until the TABLE tag is found
        $i = 0;
        while($i < count($fileContent)) {
          if (preg_match("/<table/", $fileContent[$i])) {
            $i++;
            break;
          }
          $i++;
        }
        echo $fileContent[$i]."\n";
        fclose($file);
      }
    }

    return "$filename\n";
  }

  /* Validation check for html file conversion 

  .PARAMETER $file
  The html file resource, recieved from fopen or similar.
  
  .OUTPUT 
  Returns an array containing the entire file's content. */
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
    return explode("\n", $fileContent);
  }

  /* Writes data from an array into a csv file

  .PARAMETER $file
  The csv_file resource, recieved from fopen or similar.

  .PARAMETER $rows
  A 2D array representing the (row, col) data of the spreedsheet. */
  private static function writeToCSV ($file, $rows) {
    
    foreach ($rows as $row) {
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
