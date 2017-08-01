<?php
/**
 * User: Sam Washington
 * Date: 7/19/17
 * Time: 11:34 AM
 */

namespace Sm\Query\Modules\Sql\Formatting;

use Sm\Core\Context\Context;

/**
 * Interface SqlFormattingContext
 *
 * Context defining the reason for us formatting this Sql
 *
 * @package Sm\Query\Modules\Sql\Formatting
 */
interface SqlFormattingContext extends Context {
    /**
     * Set the variables as they would be used in bind()
     *
     * @param array $variables
     *
     * @return $this
     */
    public function addVariables(array $variables);
}