<?php
/**
 * User: Sam Washington
 * Date: 7/22/17
 * Time: 1:06 AM
 */

namespace Sm\Query\Modules\Sql\Formatting\Proxy\Source\Table;


use Sm\Core\Exception\InvalidArgumentException;
use Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Data\Source\Database\Table\TableSourceSchema;

class TableSourceSchema_TableIdentifierFormattingProxy extends TableIdentifierFormattingProxy {
    /** @var  \Sm\Data\Source\Database\Table\TableSourceSchema */
    protected $subject;
    /**
     * TableSourceSchema_TableIdentifierFormattingProxy constructor.
     *
     * @param                                                            $subject
     * @param \Sm\Query\Modules\Sql\Formatting\SqlFormattingProxyFactory $formattingProxyFactory
     *
     * @throws \Sm\Core\Exception\InvalidArgumentException
     */
    public function __construct($subject, SqlFormattingProxyFactory $formattingProxyFactory = null) {
        if (!($subject instanceof TableSourceSchema)) throw new InvalidArgumentException("Can only use TableSourceSchemas for formatting here");
        
        parent::__construct($subject, $formattingProxyFactory);
    }
    
    public function getName():?string {
        return $this->subject->getName();
    }
}