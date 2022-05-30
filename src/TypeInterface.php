<?php

namespace Teto\SQL;

interface TypeInterface
{
    /**
     * @param \PDO|\Teto\SQL\PDOInterface $pdo
     * @template S of \PDOStatement|\Teto\SQL\PDOStatementInterface
     * @phpstan-param \PDO|\Teto\SQL\PDOInterface<S> $pdo
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param array<mixed> $bind_values
     * @return string|int
     */
    public function escapeValue($pdo, $key, $type, $value, &$bind_values);
}
