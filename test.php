<?php

$str = "123\n123\n123\n";
$str = explode("\n", $str);
print_r($str);
echo count($str)."\n";
foreach ($str as $row) {
  echo $row."\n";
}


?>