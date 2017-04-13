<?php

error_reporting(E_ALL);

define('TL_MODE', 'FE');
require __DIR__ . '/../../../../system/initialize.php';

$GLOBALS['TL_LANGUAGE'] = 'de';
\System::loadLanguageFile('default');