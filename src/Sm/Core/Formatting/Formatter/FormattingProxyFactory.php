<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 8:23 AM
 */

namespace Sm\Core\Formatting\Formatter;


use Sm\Core\Factory\StandardFactory;
use Sm\Core\Formatting\FormattingProxy;

/**
 * Class FormattingProxyFactory
 *
 * Factory for FormattingProxies
 *
 * @package Sm\Core\Formatting\Formatter
 */
class FormattingProxyFactory extends StandardFactory {
    public function canCreateClass($object_type) {
        return is_a($object_type, FormattingProxy::class, true);
    }
}