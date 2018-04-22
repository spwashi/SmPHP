<?php
/**
 * User: Sam Washington
 * Date: 7/6/17
 * Time: 10:21 PM
 */

namespace Sm\Query\Statements;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Source\Schema\DataSourceSchema;
use Sm\Query\Statements\Clauses\HasWhereClauseTrait;

/**
 * Class DeleteStatement
 *
 * @package Sm\Query\Types
 * @method static DeleteStatement init()
 */
class DeleteStatement extends QueryComponent {
    use HasWhereClauseTrait;
    protected $deleted_items = [];
    /** @var  array The sources that we are going to use */
    protected $from_sources = [];
    /**
     * @param mixed ...$from_sources
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
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
}