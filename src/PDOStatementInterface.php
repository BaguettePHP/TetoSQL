<?php

namespace Teto\SQL;

use PDO;

/**
 * Interface for PDOStatement compatible class
 *
 * @copyright 1997-2026 the PHP Documentation Group
 * @license https://www.php.net/manual/en/copyright.php CC-BY-3.0
 * @link    https://www.php.net/manual/class.pdostatement.php
 *
 * ## Changes
 *
 * - Prototype declarations ware converted to a valid format as PHP code.
 * - Documents that have been taken from the PHP manual was converted to PHPDoc.
 * - Part of the variable name is substituted.
 */
interface PDOStatementInterface
{
    /**
     * Bind a column to a PHP variable
     *
     * @link https://www.php.net/manual/pdostatement.bindcolumn.php
     * @param  int|string $column Number of the column (1-indexed) or name of the column in the result set. If using the column name, be aware that the name should match the case of the column, as returned by the driver.
     * @param  mixed $var Name of the PHP variable to which the column will be bound.
     * @param  int   $type Data type of the parameter, specified by the PDO::PARAM_* constants.
     * @param  int   $maxLength A hint for pre-allocation.
     * @param  mixed $driverOptions Optional parameter(s) for the driver.
     * @return bool  TRUE on success or FALSE on failure.
     */
    public function bindColumn(
        int|string $column,
        mixed &$var,
        int $type = PDO::PARAM_STR,
        int $maxLength = 0,
        mixed $driverOptions = null,
    );

    /**
     * Binds a parameter to the specified variable name
     *
     * @link https://www.php.net/manual/en/pdostatement.bindparam.php
     * @param  int|string $param Parameter identifier.  For a prepared statement using named placeholders, this will be a parameter name of the form :name.  For a prepared statement using question mark placeholders, this will be the 1-indexed position of the parameter.
     * @param  mixed $var  Name of the PHP variable to bind to the SQL statement parameter.
     * @param  int   $type Explicit data type for the parameter using the PDO::PARAM_* constants.
     * @param  int   $maxLength    Length of the data type.  To indicate that a parameter is an OUT parameter from a stored procedure, you must explicitly set the length.
     * @param  mixed $driverOptions
     * @return bool  TRUE on success or FALSE on failure.
     */
    public function bindParam(
        int|string $param,
        mixed &$var,
        int $type = PDO::PARAM_STR,
        int $maxLength = 0,
        mixed $driverOptions = null,
    );

    /**
     * Binds a value to a parameter
     *
     * @link https://www.php.net/manual/en/pdostatement.bindvalue.php
     * @param  int|string $param Parameter identifier.  For a prepared statement using named placeholders, this will be a parameter name of the form :name.  For a prepared statement using question mark placeholders, this will be the 1-indexed position of the parameter.
     * @param  mixed      $value The value to bind to the parameter.
     * @param  int        $type Explicit data type for the parameter using the PDO::PARAM_* constants.
     * @return bool       TRUE on success or FALSE on failure.
     */
    public function bindValue(int|string $param, mixed $value, int $type = PDO::PARAM_STR): bool;

    /**
     * Closes the cursor, enabling the statement to be executed again
     *
     * @link https://www.php.net/manual/en/pdostatement.closecursor.php
     * @return bool TRUE on success or FALSE on failure.
     */
    public function closeCursor(): bool;

    /**
     * Returns the number of columns in the result set
     *
     * @link https://www.php.net/manual/en/pdostatement.columncount.php
     * @return int The number of columns in the result set represented by the PDOStatement object.  If there is no result set, returns 0.
     * @phpstan-return 0|positive-int
     */
    public function columnCount(): int;

    /**
     * Dump an SQL-prepared command
     *
     * @link https://www.php.net/manual/en/pdostatement.debugdumpparams.php
     */
    public function debugDumpParams(): ?bool;

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement handle
     *
     * @link https://www.php.net/manual/en/pdostatement.errorcode.php
     * @return ?string Identical to PDO::errorCode(), except that PDOStatement::errorCode() only retrieves error codes for operations performed with PDOStatement objects.
     */
    public function errorCode(): ?string;

    /**
     * Fetch extended error information associated with the last operation on the statement handle
     *
     * @link https://www.php.net/manual/en/pdostatement.errorinfo.php
     * @return array An array of error information about the last operation performed by this statement handle.
     * @phpstan-return list{non-empty-string, ?int, ?string}
     */
    public function errorInfo(): array;

    /**
     * Executes a prepared statement
     *
     * @link https://www.php.net/manual/en/pdostatement.execute.php
     * @param  ?array $params An array of values with as many elements as there are bound parameters in the SQL statement being executed.  All values are treated as PDO::PARAM_STR.
     * @phpstan-param array<string,mixed>|null $params
     * @return bool TRUE on success or FALSE on failure.
     */
    public function execute(?array $params = null): bool;

    /**
     * Fetches the next row from a result set
     *
     * @link https://www.php.net/manual/en/pdostatement.fetch.php
     * @param  int $mode Controls how the next row will be returned to the caller. This value must be one of the PDO::FETCH_* constants, defaulting to value of PDO::ATTR_DEFAULT_FETCH_MODE (which defaults to PDO::FETCH_BOTH).
     * @param  int $cursorOrientation For a PDOStatement object representing a scrollable cursor, this value determines which row will be returned to the caller.  This value must be one of the PDO::FETCH_ORI_* constants, defaulting to PDO::FETCH_ORI_NEXT.
     * @param  int $cursorOffset For a PDOStatement object representing a scrollable cursor for which the cursor_orientation parameter is set to PDO::FETCH_ORI_ABS, this value specifies the absolute number of the row in the result set that shall be fetched.
     * @return mixed|false The return value of this function on success depends on the fetch type.  In all cases, FALSE is returned on failure.
     */
    public function fetch(
        int $mode = \PDO::FETCH_DEFAULT,
        int $cursorOrientation = \PDO::FETCH_ORI_NEXT,
        int $cursorOffset = 0,
    ): mixed;

    /**
     * Returns an array containing all of the result set rows
     *
     * @link https://www.php.net/manual/en/pdostatement.fetchall.php
     * @param  int   $mode Controls the contents of the returned array as documented in PDOStatement::fetch(). Defaults to value of PDO::ATTR_DEFAULT_FETCH_MODE (which defaults to PDO::FETCH_BOTH)
     * @param  mixed $args This argument has a different meaning depending on the value of the fetch_style parameter:
     * @param  array $ctor_args
     * @phpstan-param list<mixed> $ctor_args
     * @return array An array containing all of the remaining rows in the result set.  The array represents each row as either an array of column values or an object with properties corresponding to each column name.  An empty array is returned if there are zero results to fetch, or FALSE on failure.
     * @phpstan-return array<mixed>
     */
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed $args = null, array $ctor_args = []): array;

    /**
     * Returns a single column from the next row of a result set
     *
     * @link https://www.php.net/manual/pdostatement.fetchcolumn.php
     * @param  int $column 0-indexed number of the column you wish to retrieve from the row. If no value is supplied, PDOStatement::fetchColumn() fetches the first column.
     */
    public function fetchColumn(int $column = 0): mixed;

    /**
     * Fetches the next row and returns it as an object
     *
     * @link https://www.php.net/manual/pdostatement.fetchobject.php
     * @param ?string $class Name of the created class.
     * @phpstan-param class-string $class
     * @param  array  $constructorArgs  Elements of this array are passed to the constructor.
     * @phpstan-param list<mixed> $constructorArgs
     * @return object|false       An instance of the required class with property names that correspond to the column names or FALSE on failure.
     */
    public function fetchObject(?string $class = 'stdClass', array $constructorArgs = []): object|false;

    /**
     * Retrieve a statement attribute
     *
     * @link https://www.php.net/manual/pdostatement.getattribute.php
     * @param  int   $name Gets an attribute of the statement.
     * @return mixed Returns the attribute value.
     */
    public function getAttribute(int $name): mixed;

    /**
     * Returns metadata for a column in a result set
     *
     * @link https://www.php.net/manual/pdostatement.getcolumnmeta.php
     * @param  int $column The 0-indexed column in the result set.
     * @return array|false An associative array containing the following values representing the metadata for a single column:
     * @phpstan-return array{native_type:non-empty-string, flags:list<int>, name:string, table:string, len:int, precision:int, pdo_type:int}|false
     */
    public function getColumnMeta(int $column): array|false;

    /**
     * Advances to the next rowset in a multi-rowset statement handle
     *
     * @link https://www.php.net/manual/pdostatement.nextrowset.php
     * @return bool TRUE on success or FALSE on failure.
     */
    public function nextRowset(): bool;

    /**
     * Returns the number of rows affected by the last SQL statement
     *
     * @link https://www.php.net/manual/pdostatement.rowcount.php
     * @return int The number of rows.
     */
    public function rowCount(): int;

    /**
     * Set a statement attribute
     *
     * @link https://www.php.net/manual/en/pdostatement.setattribute.php
     * @param  int   $attribute
     * @param  mixed $value
     * @return bool  TRUE on success or FALSE on failure.
     */
    public function setAttribute(int $attribute, mixed $value): bool;

    /**
     * Set the default fetch mode for this statement
     *
     * public bool PDOStatement::setFetchMode ( int $mode )
     * public bool PDOStatement::setFetchMode ( int $PDO::FETCH_COLUMN , int $colno )
     * public bool PDOStatement::setFetchMode ( int $PDO::FETCH_CLASS , string $classname , array $ctorargs )
     * public bool PDOStatement::setFetchMode ( int $PDO::FETCH_INTO , object $object )
     *
     * @link https://www.php.net/manual/pdostatement.setfetchmode.php
     * @param  int               $mode PDO::FETCH_COLUMN|PDO::FETCH_CLASS|PDO::FETCH_INTO
     * @param  int|string|object $colno_or_classname_or_object
     * @param ?array<mixed> $ctorargs
     * @return bool              TRUE on success or FALSE on failure.
     */
    public function setFetchMode(int $mode, $colno_or_classname_or_object, ?array $ctorargs = null): bool;
}
