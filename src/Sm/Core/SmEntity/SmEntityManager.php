<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 11:26 AM
 */

namespace Sm\Core\SmEntity;

use Sm\Core\Schema\Schematic;

/**
 * Class SmEntityManager
 *
 * Class meant to manage the loading/configuration of SmEntities w/r to some layer
 *
 *
 */
interface SmEntityManager {
    public function instantiate(Schematic $identity);
    public function configure($configuration);
}