<?php
# Base
define('SM_INSTALLATION_PATH', realpath(__DIR__ . '/../') . '/');

# Testing
define('SM_TEST_PATH', SM_INSTALLATION_PATH . 'tests/');
define('SM_EXAMPLE_APP_PATH', SM_TEST_PATH . 'ExampleApp/');
define('SM_TEST_AUTOLOAD_FILE', SM_TEST_PATH . 'Sm/config/autoload.php');

# Src
define('SM_SRC_PATH', SM_INSTALLATION_PATH . 'src/');
define('SM_FRAMEWORK_PATH', SM_SRC_PATH . 'Sm/');
define('SM_VENDOR_AUTOLOAD', SM_INSTALLATION_PATH . 'vendor/autoload.php');

# Logging
define('SYSTEM_LOG_PATH', SM_INSTALLATION_PATH . 'logs/');

# Autoloading
require_once SM_FRAMEWORK_PATH . 'config/autoload.php';      # Autoloads typical classes
if (file_exists(SM_TEST_AUTOLOAD_FILE)) require_once SM_TEST_AUTOLOAD_FILE;# Autoloads testing files
if (file_exists(SM_VENDOR_AUTOLOAD)) require_once SM_VENDOR_AUTOLOAD;# Autoloads testing files
