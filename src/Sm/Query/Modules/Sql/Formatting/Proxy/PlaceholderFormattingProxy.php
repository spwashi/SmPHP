<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 11:54 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Exception\UnimplementedError;
use Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory;

/**
 * Class ColumnPlaceholderProxy
 *
 * @package Sm\Query\Modules\Sql\Formatting\Proxy\Column
 */
class PlaceholderFormattingProxy extends SqlFormattingProxy {
    protected $placeholder_name;
    protected $placeholder_value;
    /**
     * ColumnPlaceholderProxy constructor.
     *
     * @param                           $subject [name, value]
     * @param SqlFormattingProxyFactory $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\UnimplementedError
     */
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        if (!is_array($subject) || count($subject) !== 2) throw new UnimplementedError("Cannot initialize from anything but [name, column] array.");
        parent::__construct($subject, $formattingProxyFactory);
    }
    public function getPlaceholderName() {
        if (isset($this->placeholder_name)) return $this->placeholder_name;
        $placeholder_name = $this->subject[0];
        if (!is_string($placeholder_name)) throw new InvalidArgumentException("Can only use strings as placeholde names");
        return $this->placeholder_name = $placeholder_name;
    }
    public function getPlaceholderValue() {
        if (isset($this->placeholder_value)) return $this->placeholder_value;
        return $this->placeholder_value = $this->subject[1];
    }
    
}