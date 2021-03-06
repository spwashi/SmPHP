<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 7:26 PM
 */

namespace Sm\Modules\Query\Sql;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingContext;

class SqlExecutionContext implements SqlFormattingContext {
    protected $variables = [];
    use HasObjectIdentityTrait;
    
    
    public function __construct() { $this->createSelfID(); }
    public static function init() { return new static(...func_get_args()); }
    /**
     * Set the variables as they would be used in bind()
     *
     * @param array $variables
     *
     * @return $this
     */
    public function addVariables(array $variables) {
        $this->variables = array_merge($this->variables ?? [], $variables);
        return $this;
    }
    /**
     * @return mixed
     */
    public function getVariables() {
        return $this->variables;
    }
}