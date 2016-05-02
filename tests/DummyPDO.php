<?php
namespace Teto\SQL;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2016 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class DummyPDO implements PDOInterface
{
    private $executed = false;

    /**
     * @return bool
     */
    public function beginTransaction()
    {
    }

    /**
     * @return bool
     */
    public function commit()
    {
    }

    /**
     * @return string|null
     */
    public function errorCode()
    {
    }

    /**
     * @return array
     */
    public function errorInfo()
    {
    }

    /**
     * @param  string $statement
     * @return int
     */
    public function exec($statement)
    {
    }

    /**
     * @param  int
     * @return mixed
     */
    public function getAttribute($attribute)
    {
    }

    /**
     * @return string[]
     */
    public static function getAvailableDrivers()
    {
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
    }

    /**
     * @param  string $name
     * @return string
     */
    public function lastInsertId($name = null)
    {
    }

    /**
     * @param  string $statement
     * @param  array  $driver_options
     * @return \PDOStatement|false
     */
    public function prepare($statement, $driver_options = array())
    {
        return new DummyPDOStatement($statement, $driver_options);
    }

    /**
     * @param  string
     * @return \PDOStatement|false
     */
    public function query($statement)
    {
    }

    /**
     * @param  string $string
     * @param  int    $parameter_type
     * @return string
     */
    public function quote($string, $parameter_type = \PDO::PARAM_STR)
    {
        if ($parameter_type === \PDO::PARAM_STR) {
            return '@' . strtr($string, ['@' => '@@']) . '@';
        }
        if ($parameter_type === \PDO::PARAM_INT) {
            return $string;
        }
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
    }

    /**
     * @param  int
     * @param  mixed
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
    }
}
