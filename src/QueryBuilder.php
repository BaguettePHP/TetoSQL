<?php

namespace Teto\SQL;

/**
 * A generic SQL template engine (dynamic placeholder)
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class QueryBuilder
{
    /**
     * @phpstan-var list<ProcessorInterface>
     */
    private $processors;

    /**
     * @phpstan-param list<ProcessorInterface> $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * Build SQL query
     *
     * @template S of \PDOStatement|PDOStatementInterface
     * @template T of \PDO|PDOInterface<S>
     * @param \PDO|PDOInterface $pdo
     * @phpstan-param T $pdo
     * @phpstan-param non-empty-string $sql
     * @phpstan-param array<non-empty-string,mixed> $params
     * @return \PDOStatement
     * @phpstan-return ($pdo is \PDO ? \PDOStatement : S)
     */
    public function build($pdo, $sql, array $params)
    {
        $bind_values = [];
        $built_sql = $sql;
        foreach ($this->processors as $processor) {
            $built_sql = $processor->processQuery($pdo, $built_sql, $params, $bind_values);
        }

        $stmt = $pdo->prepare($built_sql);
        assert($stmt !== false);

        foreach ($bind_values as $key => $param) {
            list($type, $value) = $param;
            $stmt->bindParam($key, $value, $type);
        }

        return $stmt;
    }
}
