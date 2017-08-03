<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 12:15 PM
 */

namespace Sm\Query\Modules\Sql\Formatting;


use Sm\Core\Formatting\Formatter\FormattingProxyFactory;
use Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy;

class SqlFormattingProxyFactory extends FormattingProxyFactory {
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    public function canCreateClass($object_type) {
        return parent::canCreateClass($object_type) && is_a($object_type, SqlFormattingProxy::class, true);
    }
    /**
     * @inheritdoc
     *
     * @return SqlFormattingProxy
     */
    public function build($name = null, $item = null) {
        return parent::build($name, $item, $this);
    }
}