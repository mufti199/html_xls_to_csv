<?php declare(strict_types=1);
/*
.NAMING CONVENTION
Class, Interface, NameSpaces  - PascalCase
Function (method), Variable   - camelCase
Constants                     - SCREAMING_SNAKE_CASE

.DATA TYPES
String, Integer, Float (double), Boolean, Array, Object, NULL, Resource

.SUPER GLOBALS
$GLOBALS, $_SERVER, $_REQUEST, $_POST, $_GET, $_FILES, $_ENV, $_COOKIE, $_SESSION

.TIPS
- A class a template of an object. An object is an instace of a class.
- PHP has associative arrays which are a superset of hashtables.
- https://www.w3schools.com/PHP/php_regex.asp

.COMMON LIBRARIES
Built-in  - https://www.w3schools.com/PHP/php_ref_overview.asp
String    - https://www.w3schools.com/PHP/php_ref_string.asp
Math      - https://www.w3schools.com/PHP/php_ref_math.asp

.FORM
- Use $_GET for bookmarks and $_POST for all cases
-  

*/


// Keep an eye out for php strict and non-strict, for declarative programming, in functions chapter 
$first =/*Commenting in the middle of the code using this sytanx!*/ "last";

myFirstFunction();
echo "My $first PHP script\n";

myStaticFunction();
myStaticFunction();

define("CONSTANT", "This is a constant\n");
echo CONSTANT;

strictFunction("strict");

// Functions

function myFirstFunction() {
  // Access the global array, in which all global variables are stored
  echo "The current value of \$first is '".$GLOBALS["first"]."'\n";
  // Access the global variable
  global $first;
  $first = "first";
}

function myStaticFunction() {
  // Persist the variable within the local scope 
  static $persist = 0;
  $persist++;
  echo $persist."\n";
}

function strictFunction(string $strict): string {
  return "This is a $strict function\n";
}



/*
libxml_use_internal_errors(true);
$xml = simplexml_load_string($paresedFile);
if ($xml === false) {
  $errorMessage = "Failed loading XML: \n";
  foreach(libxml_get_errors() as $error) {
    $errorMessage .= $error->message;
  }
  throw new Exception($errorMessage);
} else {
  fwrite($csvFile, $paresedFile);
  
}
*/

/*
  /* Writes data from a column DOM object into a csv file

  .PARAMETER $file
  The csv_file resource, recieved from fopen or similar.

  .PARAMETER $cols
  All the columns of a row 
  private static function htmlWriteToCsv ($file, $cols) {
    foreach ($cols as $col) {
      $col = $col->nodeValue;
      $colLength = count($col);
      $colNum = 1;
      if ($colNum == $colLength) {
        fwrite($file, "$col\n");
      } else {
        fwrite($file, "$col,");
      }
      $colNum++;
    }
  }
}
*/

?>
