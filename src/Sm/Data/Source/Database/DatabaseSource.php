<?php
/**
 * User: Sam Washington
 * Date: 3/12/17
 * Time: 10:54 PM
 */

namespace Sm\Data\Source\Database;


use Sm\Authentication\Authentication;
use Sm\Data\Source\DataSource;
use Sm\Data\Source\Schema\NamedDataSourceSchema;

/**
 * Class DatabaseDataSource
 *
 * @package Sm\Data\Source\Database
 */
class DatabaseSource extends DataSource implements DatabaseSourceSchema, NamedDataSourceSchema {
    protected $name;
    protected $authentication;
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
    public function getName(): ?string {
        return $this->name;
    }
    public function authenticate(Authentication $authentication = null) {
        $this->authentication = $authentication;
        return $this;
    }
}