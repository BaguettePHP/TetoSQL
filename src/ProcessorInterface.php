<?php

namespace Teto\SQL;

interface ProcessorInterface
{
    /**
     * Process SQL query
     *
     * @template S of \PDOStatement|PDOStatementInterface
     * @template T of \PDO|PDOInterface<S>
     * @param \PDO|PDOInterface $pdo
     * @phpstan-param T $pdo
     * @param string $sql
     * @phpstan-param array<non-empty-string,mixed> $params
     * @phpstan-param array<non-empty-string,mixed> $bind_values
     * @return string Return processed query string
     */
    public function processQuery($pdo, $sql, array $params, array &$bind_values);
}
