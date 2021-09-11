# Plan

[x] Create a class AiaXlsFileConverter with method convertToCsv
[x] The method should read except a file directory and read it
[x] Create a new file with extension .csv after successful file validation
[x] Validaions:
  [x] The file has to have an extension of either html or xls
  [x] Is it a valid html file?
    [x] The file has the start and end with the <html></html> tags (Can also start with DOCTYPE)
    [x] The file has the <table></table> tags
[x] If the file is not an html file -> read the content and write it into the .csv file 
[x] If the file is valid html file -> read the content from the table and write it into the .csv file 

# Brief

I would like a stand-alone PHP 8 Class file developed that can receive a file location as a parameter then:

- Check if it is really an HTML file; if it is
  Convert the file to CSV
- If it actually a valid XLS file;
  Convert the file to CSV

The attached file has a .XLS extension, but it is actually poorly formatted HTML that is not readable using a standard Excel parser. You can see this by opening the file in a text editor. For this reason it needs to be converted to CSV format.

The class should throw exceptions with detailed error messages if there is any reason for the failure to parse or convert the file.

The output file name should be the same file name that was provided, only with a .csv extension instead of a .xls extension.

Ideally, I would like to interface with the class like so:

```php
<?php

try{

  $csvFileName=AiaXlsFileConverter::convertToCsv(“path/to/AIA.xls”);

  echo ‘Success: $csvFileName’;

}catch(\Exception $ex){

  echo $ex->getMessage();

}

?>
```
