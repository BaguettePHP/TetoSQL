<?php

namespace Teto\SQL;

use LogicException;

/**
 * Abstract static class of safer SQL query builder by TetoSQL
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
abstract class AbstractStaticQuery
{
    use StaticQueryExecuteTrait;

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
    public static function build($pdo, $sql, array $params)
    {
        return static::getQueryBuilder()->build($pdo, $sql, $params);
    }

    /**
     * @return QueryBuilder
     */
    protected static function getQueryBuilder()
    {
        throw new LogicException('Must be implemented ' . __FUNCTION__ . ' method in ' . __CLASS__ . '.');
    }
}
