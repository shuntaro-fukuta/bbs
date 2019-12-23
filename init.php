<?php

const DIR_SEP = DIRECTORY_SEPARATOR;

const PROJECT_ROOT  = DIR_SEP . 'var' . DIR_SEP . 'www' . DIR_SEP . 'html';

const CLASS_FILES_DIR = PROJECT_ROOT . DIR_SEP . 'classes';
const LIB_FILES_DIR   = PROJECT_ROOT . DIR_SEP . 'lib';
const HTML_FILES_DIR  = PROJECT_ROOT . DIR_SEP . 'html';
const LOG_FILES_DIR   = PROJECT_ROOT . DIR_SEP . 'logs';

require_once(PROJECT_ROOT . DIR_SEP . 'config' . DIR_SEP . 'database.php');
require_once(PROJECT_ROOT . DIR_SEP . 'functions' . DIR_SEP . 'general.php');
require_once(PROJECT_ROOT . DIR_SEP . 'database' . DIR_SEP . 'db_connect.php');
require_once(PROJECT_ROOT . DIR_SEP . 'classes' . DIR_SEP . 'ClassLoader.php');

add_include_path(CLASS_FILES_DIR);
add_include_path(LIB_FILES_DIR);

spl_autoload_register(array('ClassLoader', 'autoload'));