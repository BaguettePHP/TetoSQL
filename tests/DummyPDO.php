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
    public function beginTransaction()
    {
        throw new BadMethodCallException('Not supported');
    }

    public function commit()
    {
        throw new BadMethodCallException('Not supported');
    }

    public function errorCode()
    {
        throw new BadMethodCallException('Not supported');
    }

    public function errorInfo()
    {
        throw new BadMethodCallException('Not supported');
    }

    public function exec($statement)
    {
        throw new BadMethodCallException('Not supported');
    }

    public function getAttribute($attribute)
    {
        throw new BadMethodCallException('Not supported');
    }

    public static function getAvailableDrivers()
    {
        throw new BadMethodCallException('Not supported');
    }

    public function inTransaction()
    {
        throw new BadMethodCallException('Not supported');
    }

    public function lastInsertId($name = null)
    {
        throw new BadMethodCallException('Not supported');
    }

    public function prepare($statement, $driver_options = array())
    {
        return new DummyPDOStatement($statement, $driver_options);
    }

    public function query($statement)
    {
        throw new BadMethodCallException('Not supported');
    }

    /**
     * @param  string $string
     * @param  int    $parameter_type
     * @return string
     * @pure
     */
    public function quote($string, $parameter_type = \PDO::PARAM_STR)
    {
        if ($parameter_type === \PDO::PARAM_STR) {
            return '@' . strtr($string, ['@' => '@@']) . '@';
        }
        if ($parameter_type === \PDO::PARAM_INT) {
            return $string;
        }

        throw new LogicException("{$parameter_type} is not supported type.");
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        throw new BadMethodCallException('Not supported');
    }

    /**
     * @param int $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
        throw new BadMethodCallException('Not supported');
    }
}
