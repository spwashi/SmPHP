<?php


namespace Sm\Modules\Communication\Email\Factory;


use Sm\Core\Factory\StandardFactory;
use Sm\Modules\Communication\Email\Email;

class EmailFactory extends StandardFactory {
    protected function canCreateClass($object_type) {
        return is_a($object_type, Email::class, true);
    }
}