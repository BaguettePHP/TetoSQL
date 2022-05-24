<?php

namespace Teto\SQL;

/**
 * PCRE regular-expression based
 */
interface ReplacerInterface
{
    /**
     * Return a key of DynamicReplacer class.
     *
     * @return string
     * @phpstan-return non-empty-string
     */
    public function getKey();

    /**
     * Return a pattern PCRE regular-expression (preg)
     *
     * @return string
     * @phpstan-return non-empty-string
     */
    public function getPattern();

    /**
     * Replace the matching part by the pattern of SQL query.
     *
     * @template S of \PDOStatement|PDOStatementInterface
     * @template T of \PDO|PDOInterface<S>
     * @param \PDO|PDOInterface $pdo
     * @phpstan-param T $pdo
     * @phpstan-param array<non-empty-string,string> $matches
     * @phpstan-param array<non-empty-string,mixed> $params
     * @phpstan-param array<non-empty-string,mixed> $bind_values
     * @return string|int Return a replaced part of query string
     */
    public function replaceQuery($pdo, array $matches, array $params, array &$bind_values);
}
