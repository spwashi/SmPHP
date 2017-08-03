<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:54 PM
 */

namespace Sm\Data\Source\Database;


use Sm\Authentication\AbstractAuthentication;
use Sm\Authentication\Authentication;
use Sm\Data\Source\DataSource;
use Sm\Data\Source\Schema\NamedDataSourceSchema;

/**
 * Class DatabaseDataSource
 *
 * @package Sm\Data\Source\Database
 */
class DatabaseDataSource extends DataSource implements NamedDataSourceSchema {
    protected $name;
    /**
     * DatabaseDataSource constructor.
     *
     * @param string $name
     *
     * @internal param null|\Sm\Authentication\Authentication $Authentication The thing that will hold a reference to the connection
     */
    public function __construct(string $name = null) {
        $this->name = $name;
        parent::__construct();
    }
    /**
     * Get the name of the database
     *
     * @return mixed
     */
    public function getName(): ?string {
        return $this->name;
    }
}