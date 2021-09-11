<?php

require_once("./AiaXlsFileConverter.php");

try{

  $xls = "./AIA - sample.xls";
  $html = "./AIA - sample copy.html";
  $csvFileName=AiaXlsFileConverter::convertToCsv("123ab.xls");

  echo "Success: $csvFileName";

}catch(\Exception $ex){
  
  echo $ex->getMessage();

}

?>