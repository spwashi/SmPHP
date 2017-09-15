<?php
# Base
define('BASE_PATH', realpath(__DIR__ . '/../') . '/');

# Testing
define('TEST_PATH', BASE_PATH . 'tests/');
define('SM_TEST_PATH', TEST_PATH . 'Sm/');
define('EXAMPLE_APP_PATH', TEST_PATH . 'ExampleApp/');
define('TEST_AUTOLOAD_FILE', SM_TEST_PATH . 'config/autoload.php');

# Src
define('SRC_PATH', BASE_PATH . 'src/');
define('SM_PATH', SRC_PATH . 'Sm/');

# Logging
define('SYSTEM_LOG_PATH', BASE_PATH . 'logs/');

# Autoloading
require_once SM_PATH . 'config/autoload.php';      # Autoloads typical classes
if (file_exists(TEST_AUTOLOAD_FILE)) require_once TEST_AUTOLOAD_FILE;# Autoloads testing files
