<?php
/**
 * User: Sam Washington
 * Date: 7/1/17
 * Time: 1:53 PM
 */

namespace Sm\Core\Formatting\Formatter;


use Sm\Core\Factory\StandardFactory;
use Sm\Core\Resolvable\Resolvable;

/**
 * Class FormatterFactory
 *
 * @method Formatter build(...$arguments)
 *
 * @package Sm\Core\Formatting\Formatter
 */
class FormatterFactory extends StandardFactory {
    function __invoke() {
        return $this->format(...func_get_args());
    }
    
    /**
     * Build a Formatter and Format from it
     *
     * @param array ...$arguments
     *
     * @return mixed
     */
    public function format() {
        $arguments = func_get_args();
        if (is_null($arguments[0] ??null)) return null;
        return $this->build(...$arguments)->format(...$arguments);
    }
    /**
     * Create a formatter from a function
     *
     * @param callable $item
     *
     * @return \Sm\Core\Formatting\Formatter\Formatter
     */
    public function createFormatter(callable $item): Formatter {
        return new class($item) implements Formatter {
            /** @var callable */
            private $callback;
            public function __construct(callable $callback) { $this->callback = $callback; }
            public function format($item) {
                $callback = $this->callback;
                return $callback($item);
            }
        };
    }
    /**
     * @param object|string $object_type
     *
     * @return bool
     */
    protected function canCreateClass($object_type) {
        return is_a($object_type, Formatter::class);
    }
}