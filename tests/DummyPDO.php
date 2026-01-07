<?php

namespace Teto\SQL;

use BadMethodCallException;
use LogicException;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2016 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 * @implements PDOInterface<DummyPDOStatement>
 */
final class DummyPDO implements PDOInterface
{
    public function beginTransaction(): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function commit(): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function errorCode(): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function errorInfo(): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function exec(string $statement): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function getAttribute(int $attribute): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public static function getAvailableDrivers(): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function inTransaction(): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function lastInsertId(?string $name = null): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function prepare(string $statement, array $options = []): DummyPDOStatement
    {
        return new DummyPDOStatement($statement, $options);
    }

    public function query(string $statement, ?int $mode = null, mixed ...$fetch_mode_args): never
    {
        throw new BadMethodCallException('Not supported');
    }

    /**
     * @pure
     */
    public function quote(string $string, int $type = \PDO::PARAM_STR): string
    {
        if ($type === \PDO::PARAM_STR) {
            return '@' . strtr($string, ['@' => '@@']) . '@';
        }
        if ($type === \PDO::PARAM_INT) {
            return $string;
        }

        throw new LogicException("{$type} is not supported type.");
    }

    public function rollBack(): never
    {
        throw new BadMethodCallException('Not supported');
    }

    public function setAttribute(int $attribute, mixed $value): never
    {
        throw new BadMethodCallException('Not supported');
    }
}
