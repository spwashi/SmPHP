<?php
/**
 * User: Sam Washington
 * Date: 7/12/17
 * Time: 11:06 PM
 */

namespace Sm\Query\Statements;

use Sm\Core\Exception\InvalidArgumentException;
use Sm\Data\Source\DataSource;
use Sm\Query\Statements\Clauses\HasWhereClauseTrait;

/**
 * Class UpdateStatement
 *
 * @method static UpdateStatement init()
 */
class UpdateStatement extends QueryComponent {
    use HasWhereClauseTrait;
    protected $updated_items = [];
    protected $into_sources  = [];
    /**
     * UpdateStatement constructor.
     *
     * @param array $updates
     */
    public function __construct(...$updates) { $this->update(...$updates); }
    public function getUpdatedItems(): array {
        return $this->updated_items;
    }
    public function update(...$update_items) {
        foreach ($update_items as $item) {
            if (is_array($item)) {
                foreach ($item as $index => $value) {
                    if (is_numeric($index)) {
                        throw new InvalidArgumentException("The update array should be associative");
                    }
                }
                continue;
            }
            
            $this->into_sources[] = $this->getSourceGarage()->resolve($item);
        }
        $this->updated_items = array_merge($this->updated_items, $update_items);
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
}