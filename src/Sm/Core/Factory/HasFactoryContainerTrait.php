<?php
/**
 * User: Sam Washington
 * Date: 3/22/17
 * Time: 4:07 PM
 */

namespace Sm\Core\Factory;


trait HasFactoryContainerTrait {
    /** @var  FactoryContainer $FactoryContainer The FactoryContainer that will be used to resolve other class types */
    protected $FactoryContainer;
    /**
     * @return FactoryContainer
     */
    public function getFactoryContainer(): FactoryContainer {
        if (isset($this->FactoryContainer)) {
            return $this->FactoryContainer;
        } else {
            return $this->setFactoryContainer(new FactoryContainer)->getFactoryContainer();
        }
    }
    public function setFactoryContainer(FactoryContainer $FactoryContainer) {
        $this->FactoryContainer = $FactoryContainer;
        return $this;
    }
}