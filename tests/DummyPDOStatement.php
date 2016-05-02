<?php
namespace Teto\SQL;

/**
 * @copyright 2016 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 * @property-read string $queryString
 */
final class DummyPDOStatement implements PDOStatementInterface
{
    /** @var string */
    private $queryString;
    /** @var array */
    private $driverOptions;

    /**
     * @param string $query
     * @param array  $driver_options
     */
    public function __construct($query, array $driver_options = array())
    {
        $this->queryString   = $query;
        $this->driverOptions = $driver_options;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * @param  int|string $column
     * @param  mixed $param
     * @param  int   $type
     * @param  int   $maxlen
     * @param  mixed $driverdata
     * @return bool
     */
    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
    }

    /**
     * @param  int|string $parameter
     * @param  mixed $variable
     * @param  int   $data_type
     * @param  int   $length
     * @param  mixed $driver_options
     * @return bool
     */
    public function bindParam($parameter, &$variable, $data_type = \PDO::PARAM_STR, $length = null, $driver_options = null)
    {
    }

    /**
     * @param  int|string
     * @param  mixed
     * @param  int
     * @return bool
     */
    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR)
    {
    }

    /**
     * @return bool
     */
    public function closeCursor()
    {
    }

    /**
     * @return int
     */
    public function columnCount()
    {
    }

    /**
     * @return void
     */
    public function debugDumpParams()
    {
    }

    /**
     * @return string
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
     * @param  array $input_parameters
     * @return bool
     */
    public function execute($input_parameters)
    {
    }

    /**
     * @param  int
     * @param  int
     * @param  int
     * @return mixed|false
     */
    public function fetch($fetch_style = \PDO::ATTR_DEFAULT_FETCH_MODE, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
    }

    /**
     * @param  int
     * @param  mixed
     * @param  array
     * @return array
     */
    public function fetchAll($fetch_style, $fetch_argument = null, $ctor_args = array())
    {
    }

    /**
     * @param  int
     * @return mixed
     */
    public function fetchColumn($column_number = 0)
    {
    }

    /**
     * @param  string
     * @param  array
     * @return object|false
     */
    public function fetchObject($class_name = 'stdClass', $ctor_args = array())
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
     * @param  int
     * @return array
     */
    public function getColumnMeta($column)
    {
    }

    /**
     * @return bool
     */
    public function nextRowset(){
    }

    /**
     * @return int
     */
    public function rowCount()
    {
    }

    /**
     * @param  int   $attribute
     * @param  mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value)
    {
    }

    /**
     * @param  int
     * @param  int|string|object
     * @param  array
     * @return bool
     */
    public function setFetchMode($mode, $colno_or_classname_or_object, array $ctorargs = null)
    {
    }
}
