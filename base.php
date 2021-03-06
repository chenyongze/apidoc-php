<?php
define('DS', DIRECTORY_SEPARATOR);
define('BS', '\\');
define('BIN_PATH', ROOT_PATH . DS . 'bin');
define('LIB_PATH', ROOT_PATH . DS . 'lib');
define('CORE_PATH', LIB_PATH . DS . 'core');
define('CONF_PATH', LIB_PATH . DS . 'config');
define('COMMON_PATH', CORE_PATH . DS . 'common');
define('LANG_PATH', COMMON_PATH . DS . 'languages');
define('PARSE_PATH', COMMON_PATH . DS . 'parsers');
define('DEF_DOC_PATH', ROOT_PATH . DS . 'doc');
define('DEF_TPL_PATH', ROOT_PATH . DS . 'template');

define('NS_API', 'api');
define('NS_CORE', 'core');
define('NS_COMM', 'core' . BS . 'common');

define('APP_DEBUG', true);

define('PHP_EXT', '.php');
define('UTF8', 'utf8');