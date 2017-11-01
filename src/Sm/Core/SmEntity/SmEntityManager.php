<?php
/**
 * User: Sam Washington
 * Date: 8/3/17
 * Time: 11:26 AM
 */

namespace Sm\Core\SmEntity;

/**
 * Class SmEntityManager
 *
 * Class meant to manage the loading/configuration of SmEntities w/r to some layer
 *
 *
 */
interface SmEntityManager {
    public function instantiate($identity);
    public function configure($configuration);
}