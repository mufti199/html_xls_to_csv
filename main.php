<?php

require_once("./AiaXlsFileConverter.php");

try {
  // Test files
  $xls1 = "./123ab_single.xls";       // Single spreadsheet
  $xls2 = "./123ab_multiple.xls";     // Multiple spreadSheets
  $html1 = "./AIA - sample.xls";
  $html2 = "./AIA - sample copy.html";
  $other1 = "./AiaXlsFileConverter.php";
  $other2 = "./non.existent";

  $csvFileName=AiaXlsFileConverter::convertToCsv("");

  echo "Success: $csvFileName";

} catch(Exception $ex) {
  
  echo $ex->getMessage();

}

?>