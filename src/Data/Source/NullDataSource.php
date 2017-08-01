<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 1:44 PM
 */

namespace Sm\Data\Source;

/**
 * Class NullSource
 *
 * Represents nothing as a DataSource. Useful for things that define themselves.
 *
 * @package Sm\Data\ORM\EntityType\DataSource
 */
class NullDataSource extends DataSource {
    static $instance;
    public static function init($Authentication = null) {
        # No need to keep doing this
        return isset(static::$instance) ? static::$instance : (static::$instance = parent::init($Authentication));
    }
    public function isAuthenticated(): bool {
        return true;
    }
    public function getName() {
        return null;
    }
}