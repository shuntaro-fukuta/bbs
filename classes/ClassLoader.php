<?php

class ClassLoader
{
  public static function autoload($className)
  {
    if (class_exists($className, false)) {
      return;
    }

    $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    $includePaths = explode(PATH_SEPARATOR, get_include_path());
    foreach ($includePaths as $includePath) {
      $fullPath = $includePath . DIRECTORY_SEPARATOR . $filePath;

      if (is_readable($fullPath)) {
        require_once($fullPath);
        break;
      }
    }
  }
}
