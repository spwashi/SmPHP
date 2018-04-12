<?php


namespace Sm\Modules\Query\Sql;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Modules\Query\Sql\Formatting\SqlFormattingContext;

/**
 * Class SqlDisplayContext
 *
 * Context that means we don't add variables (everything is inline)
 */
class SqlDisplayContext implements SqlFormattingContext {
    use HasObjectIdentityTrait;
    protected $variables = [];
    public function __construct() { $this->createSelfID(); }
    public function addVariables(array $variables) {
        $this->variables = array_merge($this->variables ?? [], $variables);
        return $this;
    }
}