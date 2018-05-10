<?php
/**
 * User: Sam Washington
 * Date: 7/2/17
 * Time: 4:08 PM
 */

namespace Sm\Data\Type;

use Sm\Core\Resolvable\Resolvable;
use Sm\Core\SmEntity\SmEntity;

interface Datatype extends \JsonSerializable, SmEntity, Resolvable {
    function setSubject($subject);
}