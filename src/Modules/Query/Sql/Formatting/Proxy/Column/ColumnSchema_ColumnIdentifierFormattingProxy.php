<?php
/**
 * User: Sam Washington
 * Date: 7/14/17
 * Time: 7:44 AM
 */

namespace Sm\Modules\Query\Sql\Formatting\Proxy\Column;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Util;
use Sm\Data\Property\PropertySchema;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Modules\Query\Sql\Data\Column\ColumnSchema;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingProxyFactory;

/**
 * @inheritdoc
 * @package Sm\Modules\Query\Sql\Formatting\Proxy\Column
 */
class ColumnSchema_ColumnIdentifierFormattingProxy extends ColumnIdentifierFormattingProxy {
    /** @var  \Sm\Modules\Query\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy $table */
    protected $table;
    protected $column_name;
    /** @var  \Sm\Modules\Query\Sql\Data\Column\ColumnSchema|\Sm\Data\Property\PropertySchema $subject */
    protected $subject;
    /**
     * ColumnSchema_ColumnIdentifierFormattingProxy constructor.
     *
     * @param                           $subject
     * @param SqlFormattingProxyFactory $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        if (!($subject instanceof ColumnSchema || $subject instanceof PropertySchema)) {
            throw new InvalidArgumentException("Wrong Formatting Proxy for type [" . Util::getShape($subject) . ']');
        }
        parent::__construct($subject, $formattingProxyFactory);
    }
    public function getSource(): ?DataSourceSchema {
        if (isset($this->table)) return $this->table;
        if (!($this->subject instanceof ColumnSchema)) return null;
        $tableSchema = $this->subject->getTableSchema();
        if (!$tableSchema) return null;
        return $this->table = $tableSchema;
    }
    public function getColumnName(): ?string {
        if (isset($this->column_name)) return $this->column_name;
        return $this->column_name = $this->subject->getName();
    }
}