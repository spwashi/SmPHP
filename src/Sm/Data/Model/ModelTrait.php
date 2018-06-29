<?php


namespace Sm\Data\Model;


use Sm\Data\Property\PropertyContainer;
use Sm\Data\Property\PropertyHaver;
use Sm\Data\Source\Schema\DataSourceSchema;

trait ModelTrait {
	protected $_dataSource;

	public function getDataSource(): DataSourceSchema {
		return $this->_dataSource;
	}
	public function setDataSource(DataSourceSchema $dataSource) {
		$this->_dataSource = $dataSource;
		return $this;
	}
	/**
	 * @param      $name
	 * @param null $value
	 *
	 * @return $this
	 * @throws \Sm\Core\Exception\UnimplementedError
	 * @throws \Sm\Data\Property\Exception\NonexistentPropertyException
	 */
	public function set($name, $value = null) {
		if (is_array($name)) {
			foreach ($name as $key => $val) {
				$this->set($key, $val);
			}
		} else if ($name instanceof PropertyContainer) {
			$this->set($name->getAll());
		} else {
			/** @var PropertyContainer $properties */
			$properties = $this->getProperties();
			$properties->set($name, $value);
		}
		return $this;
	}
}