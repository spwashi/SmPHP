<?php
/**
 * User: Sam Washington
 * Date: 7/17/17
 * Time: 7:47 PM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Column;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Formatting\Formatter\FormattingProxyFactory;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy;
use Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table\TableFormattingProxy;

class String_ColumnIdentifierFormattingProxy extends ColumnIdentifierFormattingProxy {
    protected $subject;
    #
    protected $column_name;
    protected $table;
    protected $type;
    protected $length;
    protected $default;
    #
    protected $format_as_string = false;
    #
    public function __construct($column, FormattingProxyFactory $formattingProxyFactory) {
        if (!is_string($column)) throw new UnimplementedError("+ format anything but a string as a column");
        parent::__construct($column, $formattingProxyFactory);
    }
    /**
     * @return null|\Sm\Query\Modules\Sql\Formatting\Proxy\SqlFormattingProxy|TableFormattingProxy
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function getSource(): ?DataSourceSchema {
        if (isset($this->table)) return $this->table;
        
        
        if (strpos($this->subject, '.') === false) return null;
        
        $table_name = null;
        $explode    = explode('.', $this->subject);
        $count      = count($explode);
        if ($count === 2) {
            $table_name = $explode[0];
        } else if ($count === 3) {
            $table_name = $explode[1];
        } else {
            throw new InvalidArgumentException("Improper subject for table");
        }
        
        return $this->table = $this->getFormattingProxyFactory()->build(NamedDataSourceFormattingProxy::class, $table_name);
    }
    /**
     * Returns the assumed name of the column based on everything we know
     *
     * @return null|string
     */
    public function getColumnName():?string {
        if (isset($this->column_name)) return $this->column_name;
        # If we are doing something separated by column name
        if (strpos($this->subject, '.')) {
            $explode = explode('.', $this->subject);
            return $this->column_name = end($explode);
        }
        
        #todo check to see if the column name is malformed?
        
        return $this->column_name = $this->subject;
    }
    public function formatAsString() {
        $this->format_as_string = true;
        return $this;
    }
    public function doFormatAsString() {
        return $this->format_as_string;
    }
}