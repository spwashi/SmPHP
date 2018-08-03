<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/27/18
 * Time: 1:37 PM
 */

namespace Sm\Data\Property\Context;


use Sm\Data\Property\Property;
use Sm\Data\Type\Undefined_;

/**
 * Class DirtyProperty
 *
 * A Property that represents a value that's been literally set (without any preparation).
 *
 * Should only really exist in a 'null' context
 *
 * @package Sm\Data\Property\Context
 */
class DirtyProperty extends Property {

    public function setSubject($subject, $do_track_change = true) {
        # dirty properties do not store any history except to mark them as not undefined

        $this->resetValueHistory();
        $this->markValueChange($subject, Undefined_::init());

        return $this;
    }
}