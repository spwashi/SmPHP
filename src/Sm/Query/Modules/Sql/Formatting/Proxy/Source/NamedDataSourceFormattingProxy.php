<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 7:39 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Source;

use Sm\Core\Exception\UnimplementedError;
use Sm\Data\Source\Schema\NamedDataSourceSchema;
use Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy;

/**
 * Interface NamedDataSourceFormattingProxy
 *
 * Meant to serve as the basis for DataSourceFormattingProxies that have names
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\Source
 */
class NamedDataSourceFormattingProxy extends SqlFormattingProxy implements NamedDataSourceSchema {
    public function getName(): ? string {
        if (is_string($this->subject)) return $this->subject;
        if ($this->subject instanceof NamedDataSourceSchema) return $this->subject->getName();
        throw new UnimplementedError("Can only format DataSources that have a name");
    }
}