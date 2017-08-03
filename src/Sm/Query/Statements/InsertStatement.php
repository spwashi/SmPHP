<?php
/**
 * User: Sam Washington
 * Date: 7/13/17
 * Time: 12:02 AM
 */

namespace Sm\Query\Statements;

use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Source\DataSource;

/**
 * Class InsertStatement
 *
 * @package Sm\Query\Statements
 */
class InsertStatement extends QueryComponent {
    protected $into_sources   = [];
    protected $inserted_items = [];
    /**
     * The values we want to set with this Query
     *
     * @param array ...$inserted_items
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function set(...$inserted_items) {
        foreach ($inserted_items as $item) {
            if (is_array($item)) {
                foreach ($item as $index => $value) {
                    if (is_numeric($index)) throw new InvalidArgumentException("The insert array should be associative");
                }
                continue;
            }
            $this->into_sources[] = $this->getSourceGarage()->resolve($item);
        }
        $this->inserted_items = array_merge($this->inserted_items, $inserted_items);
        return $this;
    }
    /**
     * Set the Sources in which we should update
     *
     * @param array ...$sources
     *
     * @return $this
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function inSources(...$sources) {
        foreach ($sources as $source) {
            if (is_string($source)) {
                if (strpos($source, '\\')) throw new InvalidArgumentException("Cannot set Source to be a classname");
            } else if (!($source instanceof DataSource)) {
                throw new InvalidArgumentException("Cannot set source to be something that is not a DataSource");
            }
            
        }
        $this->into_sources = array_merge($this->into_sources, $sources);
        return $this;
    }
    public function getIntoSources(): array {
        return $this->into_sources;
    }
    /**
     * @return array
     */
    public function getInsertedItems(): array {
        return $this->inserted_items;
    }
}