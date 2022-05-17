<?php

namespace Teto\SQL;

use BadMethodCallException;

/**
 * @copyright 2016 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 * @property-read string $queryString
 */
final class DummyPDOStatement implements PDOStatementInterface
{
    /** @var string */
    private $queryString;
    /** @var array<mixed> */
    private $driverOptions;

    /**
     * @param string $query
     * @param array<mixed> $driver_options
     */
    public function __construct($query, array $driver_options = array())
    {
        $this->queryString = $query;
        $this->driverOptions = $driver_options;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function bindParam($parameter, &$variable, $data_type = \PDO::PARAM_STR, $length = null, $driver_options = null)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function closeCursor()
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function columnCount()
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function debugDumpParams()
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function errorCode()
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function errorInfo()
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function execute($input_parameters)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetch($fetch_style = \PDO::ATTR_DEFAULT_FETCH_MODE, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetchAll($fetch_style, $fetch_argument = null, $ctor_args = array())
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetchColumn($column_number = 0)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetchObject($class_name = 'stdClass', $ctor_args = array())
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function getAttribute($attribute)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function getColumnMeta($column)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function nextRowset()
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function rowCount()
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function setAttribute($attribute, $value)
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function setFetchMode($mode, $colno_or_classname_or_object, array $ctorargs = null)
    {
        throw new BadMethodCallException('Unexpected method call');
    }
}
