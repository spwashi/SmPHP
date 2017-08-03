<?php
/**
 * User: Sam Washington
 * Date: 7/10/17
 * Time: 6:57 PM
 */

namespace Sm\Data\Source\Schema;

use Sm\Core\Abstraction\Registry;
use Sm\Core\Exception\InvalidArgumentException;
use Sm\Core\Resolvable\Error\UnresolvableException;
use Sm\Data\Source\DataSourceContainer;

/**
 * Class DataSourceSchemaGarage
 *
 * Class that holds or creates DataSourceSchemas. Meant to resolve the Source(s) of items that belong to the Data layer
 *
 * @package Sm\Data\Source
 */
class DataSourceSchemaGarage implements Registry {
    /** @var \Sm\Data\Source\DataSourceFactory $dataSourceSchemaFactory */
    private $dataSourceSchemaFactory;
    /** @var \Sm\Data\Source\DataSourceContainer $dataSourceContainer */
    private $dataSourceContainer;
    
    /**
     * SourceGarage constructor.
     *
     * @param \Sm\Data\Source\DataSourceFactory   $dataSourceFactory   The Factory that will create Sources
     * @param \Sm\Data\Source\DataSourceContainer $dataSourceContainer The Container that will hold Sources
     */
    public function __construct(DataSourceContainer $dataSourceContainer = null, DataSourceSchemaFactory $dataSourceFactory = null) {
        $this->dataSourceSchemaFactory = $dataSourceFactory ?? new DataSourceSchemaFactory;
        $this->dataSourceContainer     = $dataSourceContainer ?? new DataSourceContainer;
    }
    public function register($name, $registrand = null) {
        if (!$registrand) $this->dataSourceSchemaFactory->register($name);
        else $this->dataSourceContainer->register($name, $registrand);
        return $this;
    }
    public function resolve($item = null) {
        if (!$item) throw new InvalidArgumentException("Cannot resolve Null");
        $result = $this->dataSourceContainer->resolve($item);
        if ($result) return $result;
        $result = $this->dataSourceSchemaFactory->resolve($item);
        if ($result) return $result;
        throw new UnresolvableException("Cannot find matching source.");
    }
}