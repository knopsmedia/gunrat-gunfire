<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Repository;

use Doctrine\DBAL\Connection;

abstract class AbstractDbalRepository
{
    private Connection $connection;

    private string $tableName;
    private array $columnDefinitions;

    public function __construct(Connection $connection, string $tableName, array $columnDefinition)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->columnDefinitions = $columnDefinition;
    }

    protected function getTableName(): string
    {
        return $this->tableName;
    }

    protected function getColumnDefinitions(): array
    {
        return $this->columnDefinitions;
    }

    protected function getConnection(): Connection
    {
        return $this->connection;
    }
}