<?php
/**
 * 'g.'User: Sam Washington
 * Date: 7/24/17
 * Time: 8:51 PM
 **/

use Sm\Core\Exception\UnimplementedError;
use Sm\Core\Resolvable\StringResolvable;
use Sm\Core\Util;
use Sm\Data\Evaluation\TwoOperandStatement;
use Sm\Data\Source\Constructs\JoinedSourceSchematic;
use Sm\Data\Source\Database\Table\TableSource;
use Sm\Data\Source\Schema\NamedDataSourceSchema;
use Sm\Modules\Sql\Constraints\ForeignKeyConstraintSchema;
use Sm\Modules\Sql\Constraints\PrimaryKeyConstraintSchema;
use Sm\Modules\Sql\Constraints\UniqueKeyConstraintSchema;
use Sm\Modules\Sql\Data\Column\ColumnSchema;
use Sm\Modules\Sql\Data\Column\DateTimeColumnSchema;
use Sm\Modules\Sql\Data\Column\IntegerColumnSchema;
use Sm\Modules\Sql\Formatting\Clauses\ConditionalClauseFormatter;
use Sm\Modules\Sql\Formatting\Column\ColumnSchemaFormatter;
use Sm\Modules\Sql\Formatting\Column\DateTimeColumnSchemaFormatter;
use Sm\Modules\Sql\Formatting\Column\IntegerColumnSchemaFormatter;
use Sm\Modules\Sql\Formatting\Component\ColumnIdentifierFormattingProxyFormatter;
use Sm\Modules\Sql\Formatting\Component\SelectExpressionFormattingProxyFormatter;
use Sm\Modules\Sql\Formatting\Component\String_ColumnIdentifierFormattingProxyFormatter;
use Sm\Modules\Sql\Formatting\Component\TwoOperandStatementFormatter;
use Sm\Modules\Sql\Formatting\Proxy\Column\ColumnIdentifierFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\Column\ColumnSchema_ColumnIdentifierFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\Column\String_ColumnIdentifierFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\Component\SelectExpressionFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\PlaceholderFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\Source\NamedDataSourceFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\Source\Table\String_TableIdentifierFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\Source\Table\TableIdentifierFormattingProxy;
use Sm\Modules\Sql\Formatting\Proxy\Source\Table\TableSourceSchema_TableIdentifierFormattingProxy;
use Sm\Modules\Sql\Formatting\Source\JoinedSourceSchemaFormatter;
use Sm\Modules\Sql\Formatting\SqlFormattingProxyFactory;
use Sm\Modules\Sql\Formatting\SqlQueryFormatterManager;
use Sm\Modules\Sql\Formatting\Statements\InsertStatementFormatter;
use Sm\Modules\Sql\Formatting\Statements\SelectStatementFormatter;
use Sm\Modules\Sql\Formatting\Statements\Table\AlterTableStatementFormatter;
use Sm\Modules\Sql\Formatting\Statements\Table\CreateTableStatementFormatter;
use Sm\Modules\Sql\Formatting\Statements\UpdateStatementFormatter;
use Sm\Modules\Sql\Formatting\StdSqlFormatter;
use Sm\Modules\Sql\Statements\AlterTableStatement;
use Sm\Modules\Sql\Statements\CreateTableStatement;
use Sm\Query\Exception\ImproperlyFormedQueryException;
use Sm\Query\Proxy\String_QueryProxy;
use Sm\Query\Statements\Clauses\ConditionalClause;
use Sm\Query\Statements\InsertStatement;
use Sm\Query\Statements\SelectStatement;
use Sm\Query\Statements\UpdateStatement;

/**
 * Register the methods/classes we will use when trying to proxy an item in a particular way.
 * Primarily helpful in building components of queries for a specific purpose.
 *
 * @param SqlFormattingProxyFactory $formattingProxyFactory
 */
function register_proxy_handlers(SqlFormattingProxyFactory $formattingProxyFactory) {
    $formattingProxyFactory->register([
                                          ColumnIdentifierFormattingProxy::class                  => function ($item, SqlFormattingProxyFactory $formattingProxyFactory) {
                                              if ($item instanceof ColumnSchema) {
                                                  return $formattingProxyFactory->build(ColumnSchema_ColumnIdentifierFormattingProxy::class, $item);
                                              }
    
                                              if ($item instanceof StringResolvable) {
                                                  $item = "$item";
                                                  /** @var String_ColumnIdentifierFormattingProxy $string_ColumnIdentifierFormattingProxy */
                                                  $string_ColumnIdentifierFormattingProxy = $formattingProxyFactory->build(String_ColumnIdentifierFormattingProxy::class, $item);
                                                  $string_ColumnIdentifierFormattingProxy->formatAsString();
                                                  return $string_ColumnIdentifierFormattingProxy;
                                              }
            
                                              if (is_string($item)) {
                                                  return $formattingProxyFactory->build(String_ColumnIdentifierFormattingProxy::class, $item);
                                              }
                                              throw new UnimplementedError('+ Anything but strings [' . Util::getShape($item) . ']');
                                          },
                                          ColumnSchema_ColumnIdentifierFormattingProxy::class     => ColumnSchema_ColumnIdentifierFormattingProxy::class,
                                          String_ColumnIdentifierFormattingProxy::class           => String_ColumnIdentifierFormattingProxy::class,
                                          String_TableIdentifierFormattingProxy::class            => String_TableIdentifierFormattingProxy::class,
                                          TableSourceSchema_TableIdentifierFormattingProxy::class => TableSourceSchema_TableIdentifierFormattingProxy::class,
                                          TableIdentifierFormattingProxy::class                   => function ($item, SqlFormattingProxyFactory $formattingProxyFactory) {
                                              if ($item instanceof TableSource) {
                                                  return $formattingProxyFactory->build(TableSourceSchema_TableIdentifierFormattingProxy::class, $item);
                                              }
            
            
                                              if ($item instanceof NamedDataSourceSchema) $item = $item->getName();
            
                                              # Default to formatting tables as strings
                                              if (is_string($item)) {
                                                  return $formattingProxyFactory->build(String_TableIdentifierFormattingProxy::class, $item);
                                              }
    
                                              throw new UnimplementedError('+ Anything but strings[' . Util::getShape($item) . ']');
                                          },
                                      ]);
}

function register_formatting_handlers(SqlQueryFormatterManager $formatterManager) {
    $formatterFactory = $formatterManager->formatterFactory;
    $formatterFactory->register(
        [
            new StdSqlFormatter,
            String_QueryProxy::class                      => $formatterFactory->createFormatter(function (String_QueryProxy $proxy) {
                return $proxy->getQuery();
            }),
            StringResolvable::class                       => $formatterFactory->createFormatter(function (StringResolvable $stringResolvable) {
                return '"' . $stringResolvable . '"';
            }),
            SelectStatement::class                        => new SelectStatementFormatter($formatterManager),
            UpdateStatement::class                        => new UpdateStatementFormatter($formatterManager),
            CreateTableStatement::class                   => new CreateTableStatementFormatter($formatterManager),
            AlterTableStatement::class                    => new AlterTableStatementFormatter($formatterManager),
            InsertStatement::class                        => new InsertStatementFormatter($formatterManager),
            ConditionalClause::class                      => new ConditionalClauseFormatter($formatterManager),
            ColumnSchema::class                           => new ColumnSchemaFormatter($formatterManager),
            IntegerColumnSchema::class                    => new IntegerColumnSchemaFormatter($formatterManager),
            DateTimeColumnSchema::class                   => new DateTimeColumnSchemaFormatter($formatterManager),
            ColumnIdentifierFormattingProxy::class        => new ColumnIdentifierFormattingProxyFormatter($formatterManager),
            String_ColumnIdentifierFormattingProxy::class => new String_ColumnIdentifierFormattingProxyFormatter($formatterManager),
            TwoOperandStatement::class                    => new TwoOperandStatementFormatter($formatterManager),
            PlaceholderFormattingProxy::class             =>
                $formatterFactory->createFormatter(function (PlaceholderFormattingProxy $columnSchema) use ($formatterFactory) {
                    $placeholderName = $columnSchema->getPlaceholderName();
                    return $placeholderName ? ":{$placeholderName}" : '?';
                }),
    
            JoinedSourceSchematic::class           => new JoinedSourceSchemaFormatter($formatterManager),
            SelectExpressionFormattingProxy::class => new SelectExpressionFormattingProxyFormatter($formatterManager),
            PrimaryKeyConstraintSchema::class      =>
                $formatterFactory->createFormatter(function (PrimaryKeyConstraintSchema $primaryKeyConstraintSchema) use ($formatterFactory) {
                    $columns      = $primaryKeyConstraintSchema->getColumns();
                    $column_names = [];
                    foreach ($columns as $column) {
                        $column_names[] = $column->getName();
                    }
                    $column_name_string = join(', ', $column_names);
                    return "PRIMARY KEY({$column_name_string})";
                }),
            UniqueKeyConstraintSchema::class       =>
                $formatterFactory->createFormatter(function (UniqueKeyConstraintSchema $primaryKeyConstraintSchema) use ($formatterFactory) {
                    $columns      = $primaryKeyConstraintSchema->getColumns();
                    $column_names = [];
                    foreach ($columns as $column) {
                        $column_names[] = $column->getName();
                    }
                    $column_name_string = join(', ', $column_names);
                    return "UNIQUE KEY({$column_name_string})";
                }),
            ForeignKeyConstraintSchema::class      =>
                $formatterFactory->createFormatter(function (ForeignKeyConstraintSchema $foreignKeyConstraintSchema) use ($formatterFactory) {
                    $column_array            = $foreignKeyConstraintSchema->getColumns();
                    $referenced_column_array = $foreignKeyConstraintSchema->getReferencedColumns();
    
                    # Get each referencing column name
                    $column_names = [];
                    foreach ($column_array as $column) {
                        $column_names[] = $column->getName();
                    }
    
                    # Get each referenced column name
                    $table_name              = null;
                    $referenced_column_names = [];
                    foreach ($referenced_column_array as $referenced_column) {
    
                        # Set the table name being referenced
                        $referenced_table = $referenced_column->getTableSchema();
                        $new_table_name   = $referenced_table ? $referenced_table->getName() ?? $table_name : $table_name;
                        if ($table_name !== $new_table_name && isset($table_name)) {
                            throw new UnimplementedError("Cannot form ForeignKeyConstraints with multiple tables referenced");
                        } else {
                            $table_name = $new_table_name;
                        }
    
                        # Add the referenced column name
                        $referenced_column_names[] = $referenced_column->getName();
                    }
    
                    # Throw an error if there is no matching table name
                    if (!isset($table_name)) throw new ImproperlyFormedQueryException("Cannot form foreign key without knowing referenced table");
    
                    $column_name_string            = join(', ', $column_names);
                    $referenced_column_name_string = join(', ', $referenced_column_names);
    
                    # Format the statement
                    $query = "FOREIGN KEY ({$column_name_string})\nREFERENCES {$table_name} ($referenced_column_name_string)";
    
                    # If there is a constraint name, allow us to name it
                    $constraintName = $foreignKeyConstraintSchema->getConstraintName();
                    if (isset($constraintName)) $query = "CONSTRAINT {$constraintName} {$query}";
    
    
                    return $query;
                }),
            NamedDataSourceFormattingProxy::class  =>
                $formatterFactory->createFormatter(function (NamedDataSourceFormattingProxy $tableNameFormattingProxy) {
                    return '`' . $tableNameFormattingProxy->getName() . '`';
                }),
            TableIdentifierFormattingProxy::class  =>
                $formatterFactory->createFormatter(function (TableIdentifierFormattingProxy $columnFormattingProxy) use ($formatterFactory) {
                    $formatted_table = '`' . $columnFormattingProxy->getName() . '`';
                    return $formatted_table;
                }),
        ]);
}