<?php

class Logger
{
  protected $dir   = '';
  protected $files = array();

  public function __construct($dir = null)
  {
    $this->setDir($dir);

    register_shutdown_function(array($this, 'shutdown'));
  }

  public function setDir($dir, $append = true)
  {
    if (empty($dir)) {
      if (defined('LOG_FILES_DIR')) {
        $dir = LOG_FILES_DIR;
      } else {
        throw new Exception(__METHOD__ . '() You must specify the directory.');
      }
    }

    if (file_exists($dir) && is_file($dir)) {
      throw new Exception(__METHOD__ . "() '{$dir}' is a file.");
    }

    if (!file_exists($dir)) {
      if ($append) {
        if (!mkdir($dir, 0777, true)) {
          throw new Exception(__METHOD__ . "() Failed to create directory '{$dir}'.");
        }
      } else {
        throw new Exception(__METHOD__ . "() Directory not found. '{$dir}'");
      }
    }

    $this->dir = $dir;
  }

  public function write($message, $errType = 'E_ALL', $name = 'default')
  {
    $fileName = $name . '.log';

    if (!isset($this->files[$fileName])) {
      $this->files[$fileName] = fopen($this->dir . '/' . $fileName, 'a+');
    }

    $log = '[' . date('Y/m/d H:i:s') . '] ' . $this->errTypeToString($errType) . ' ' . $message;
    fwrite($this->files[$fileName], $log . PHP_EOL);
  }

  public function shutdown()
  {
    foreach ($this->files as $name => $fp) {
      fclose($fp);
    }

    $this->files = array();
  }

  protected function errTypeToString($type)
  {
    switch ($type) {
      case E_ERROR:
        return 'E_ERROR';
      case E_WARNING:
        return 'E_WARNING';
      case E_PARSE:
        return 'E_PARSE';
      case E_NOTICE:
        return 'E_NOTICE';
      case E_CORE_ERROR:
        return 'E_CORE_ERROR';
      case E_CORE_WARNING:
        return 'E_CORE_WARNING';
      case E_CORE_ERROR:
        return 'E_COMPILE_ERROR';
      case E_CORE_WARNING:
        return 'E_COMPILE_WARNING';
      case E_USER_ERROR:
        return 'E_USER_ERROR';
      case E_USER_WARNING:
        return 'E_USER_WARNING';
      case E_USER_NOTICE:
        return 'E_USER_NOTICE';
      case E_STRICT:
        return 'E_STRICT';
      default:
        if (defined('E_RECOVERABLE_ERROR') && $type === E_RECOVERABLE_ERROR) {
          return 'E_RECOVERABLE_ERROR';
        } elseif (defined('E_DEPRECATED') && $type === E_DEPRECATED) {
          return 'E_DEPRECATED';
        } elseif (defined('E_USER_DEPRECATED') && $type === E_USER_DEPRECATED) {
          return 'E_USER_DEPRECATED';
        } else {
          return $type;
        }
    }
  }
}
