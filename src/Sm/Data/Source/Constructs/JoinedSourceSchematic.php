<?php
/**
 * User: Sam Washington
 * Date: 7/21/17
 * Time: 12:25 AM
 */

namespace Sm\Data\Source\Constructs;


use Sm\Core\Internal\Identification\HasObjectIdentityTrait;
use Sm\Core\Internal\Identification\Identifiable;
use Sm\Data\Evaluation\EvaluableStatement;
use Sm\Data\Source\Schema\DataSourceSchema;

/**
 * Class JoinedSource
 *
 * @package Sm\Data\Source\Constructs
 */
class JoinedSourceSchematic implements JoinedSourceSchema, Identifiable {
    use HasObjectIdentityTrait;
    protected $origin_sources;
    protected $joined_sources;
    protected $join_conditions;
    public function __construct() {
        $this->createSelfID();
    }
    public static function init() {
        return new static(...func_get_args());
    }
    public function getOriginSources(): ?array {
        return $this->origin_sources;
    }
    /**
     * @param \Sm\Data\Source\Schema\DataSourceSchema[] ...$origin
     *
     * @return $this
     */
    public function setOriginSources(DataSourceSchema ...$origin) {
        $this->origin_sources = $origin;
        return $this;
    }
    public function getJoinedSources(): ?array {
        return $this->joined_sources;
    }
    /**
     * @param \Sm\Data\Source\Schema\DataSourceSchema[] ...$join
     *
     * @return $this
     */
    public function setJoinedSources(DataSourceSchema ...$join) {
        $this->joined_sources = $join;
        return $this;
    }
    public function getJoinConditions(): ?array {
        return $this->join_conditions;
    }
    public function setJoinConditions(EvaluableStatement ...$conditions) {
        $this->join_conditions = $conditions;
        return $this;
    }
}