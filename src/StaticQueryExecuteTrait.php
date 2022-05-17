<?php

namespace Teto\SQL;

/**
 * A trait of static execute() and executeAndReturnInsertId() method
 *
 * @copyright 2016 pixiv Inc.
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
trait StaticQueryExecuteTrait
{
    /**
     * Build SQL query and execute
     *
     * @template S of \PDOStatement|PDOStatementInterface
     * @template T of \PDO|PDOInterface<S>
     * @param \PDO|PDOInterface|PDOAggregate $pdo
     * @phpstan-param T|PDOAggregate<T> $pdo
     * @param string $sql
     * @phpstan-param non-empty-string $sql
     * @phpstan-param array<non-empty-string,mixed> $params
     * @return \PDOStatement|PDOStatementInterface
     * @phpstan-return ($pdo is \PDO ? \PDOStatement : S)
     */
    public static function execute($pdo, $sql, array $params)
    {
        if ($pdo instanceof PDOAggregate) {
            /** @phpstan-var T $pdo */
            $pdo = $pdo->getPDO();
        }

        $stmt = static::build($pdo, $sql, $params);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Build SQL query and execute
     *
     * @template S of \PDOStatement|PDOStatementInterface
     * @template T of \PDO|PDOInterface<S>
     * @param \PDO|PDOInterface|PDOAggregate $pdo
     * @phpstan-param T|PDOAggregate<T> $pdo
     * @param string $sql
     * @phpstan-param non-empty-string $sql
     * @phpstan-param array<non-empty-string,mixed> $params
     * @param ?string $name
     * @return string
     */
    public static function executeAndReturnInsertId($pdo, $sql, array $params, $name = null)
    {
        if ($pdo instanceof PDOAggregate) {
            /** @phpstan-var T $pdo */
            $pdo = $pdo->getPDO();
        }

        $stmt = static::build($pdo, $sql, $params);
        $stmt->execute();

        $id = $pdo->lastInsertId($name);
        assert($id !== false);

        return $id;
    }
}
