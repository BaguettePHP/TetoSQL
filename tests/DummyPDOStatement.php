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
    private string $queryString;
    /** @var array<mixed> */
    private array $driverOptions;

    /**
     * @param string $query
     * @param array<mixed> $driver_options
     */
    public function __construct(string $query, array $driver_options = [])
    {
        $this->queryString = $query;
        $this->driverOptions = $driver_options;
    }

    public function __get(string $name): mixed
    {
        return $this->$name;
    }

    public function bindColumn(int|string $column, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function bindParam(int|string $param, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function bindValue(int|string $param, mixed $value, int $type = \PDO::PARAM_STR): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function closeCursor(): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function columnCount(): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function debugDumpParams(): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function errorCode(): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function errorInfo(): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function execute(?array $params = null): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetch(int $mode = \PDO::FETCH_DEFAULT, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetchAll(int $mode = \PDO::FETCH_DEFAULT, mixed $args = null, array $ctor_args = []): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetchColumn(int $column = 0): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function fetchObject(?string $class = 'stdClass', array $constructorArgs = []): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function getAttribute(int $name): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function getColumnMeta(int $column): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function nextRowset(): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function rowCount(): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function setAttribute(int $attribute, mixed $value): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }

    public function setFetchMode(int $mode, $colno_or_classname_or_object = null, ?array $ctorargs = null): never
    {
        throw new BadMethodCallException('Unexpected method call');
    }
}
