<?php
/**
 * User: Sam Washington
 * Date: 7/6/17
 * Time: 10:21 PM
 */

namespace Sm\Query\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Factory\Exception\FactoryCannotBuildException;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Query\Statements\Clauses\HasWhereClauseTrait;

/**
 * Class SelectStatement
 *
 * @package Sm\Query\Types
 * @method static SelectStatement init()
 */
class SelectStatement extends QueryComponent {
    use HasWhereClauseTrait;
    protected $selected_items = [];
    /** @var  array The sources that we are going to use */
    protected $from_sources = [];
    public function __construct(...$items) { $this->select(...$items); }
    /**
     * Select items (often Schemas) Usually these are going to be things like Columns
     *
     * @param mixed ...$select_items
     *
     * @return $this
     */
    public function select(...$select_items) {
        foreach ($select_items as $item) {
            if (is_string($item)) continue;
            try {
                $this->from_sources[] = $this->getSourceGarage()->resolve($item);
            } catch (FactoryCannotBuildException $exception) {
        
            }
        }
        $this->selected_items = array_merge($this->selected_items, $select_items);
        return $this;
    }
    public function from(...$from_sources) {
        foreach ($from_sources as $source) {
            if (is_string($source)) {
                if (strpos($source, '\\')) throw new InvalidArgumentException("Cannot set Source to be a classname");
            } else if (!($source instanceof DataSourceSchema)) {
                throw new InvalidArgumentException("Cannot set source to be something that is not a DataSource");
            }
        }
        $this->from_sources = array_merge($this->from_sources, $from_sources);
        return $this;
    }
    /**
     * Return the sources used
     */
    public function getFromSources() { return array_unique($this->from_sources, SORT_REGULAR); }
    
    /**
     * Get the items that are going to be selected
     *
     * @return array
     */
    public function getSelectedItems(): array { return $this->selected_items; }
}