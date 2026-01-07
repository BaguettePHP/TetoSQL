<?php

namespace Teto\SQL;

/**
 * Interface for PHP Data Objects (PDO) compatible class
 *
 * @copyright 1997-2026 the PHP Documentation Group
 * @license https://www.php.net/manual/en/copyright.php CC-BY-3.0
 * @link https://www.php.net/manual/class.pdo.php
 *
 * ## Changes
 *
 * - Prototype declarations ware converted to a valid format as PHP code.
 * - Documents that have been taken from the PHP manual was converted to PHPDoc.
 * - Part of the variable name is substituted.
 *
 * @template T of \PDOStatement|PDOStatementInterface
 * @phpstan-type teto_pdo \PDO|PDOInterface
 * @phpstan-type teto_pdo_statement \PDOStatement|PDOStatementInterface
 */
interface PDOInterface
{
    /**
     * Initiates a transaction
     *
     * @link https://www.php.net/manual/pdo.begintransaction.php
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function beginTransaction(): bool;

    /**
     * Commits a transaction
     *
     * @link https://www.php.net/manual/pdo.commit.php
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function commit(): bool;

    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle
     *
     * @link https://www.php.net/manual/pdo.errorcode.php
     * @return string|null Returns an SQLSTATE, a five characters alphanumeric identifier defined in the ANSI SQL-92 standard.  Returns NULL if no operation has been run on the database handle.
     */
    public function errorCode(): ?string;

    /**
     * Fetch extended error information associated with the last operation on the database handle
     *
     * @link https://www.php.net/manual/pdo.errorinfo.php
     * @return array<string,mixed> Returns an array of error information about the last operation performed by this database handle.
     */
    public function errorInfo(): array;

    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @link https://www.php.net/manual/pdo.exec.php
     * @param  string $statement The SQL statement to prepare and execute.
     * @return int|false Returns the number of rows that were modified or deleted by the SQL statement you issued.  If no rows were affected, returns 0.
     */
    public function exec(string $statement): int|false;

    /**
     * Retrieve a database connection attribute
     *
     * @link https://www.php.net/manual/pdo.getattribute.php
     * @param  int $attribute One of the PDO::ATTR_* constants.
     * @return mixed
     */
    public function getAttribute(int $attribute): mixed;

    /**
     * Return an array of available PDO drivers
     *
     * @link https://www.php.net/manual/pdo.getavailabledrivers.php
     * @return string[]
     */
    public static function getAvailableDrivers(): array;

    /**
     * Checks if inside a transaction
     *
     * @link https://www.php.net/manual/pdo.intransaction.php
     * @return bool Returns TRUE if a transaction is currently active, and FALSE if not.
     */
    public function inTransaction(): bool;

    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @link https://www.php.net/manual/pdo.lastinsertid.php
     * @param  string|null $name
     * @return string|false
     */
    public function lastInsertId(?string $name = null): string|false;

    /**
     * Prepares a statement for execution and returns a statement object
     *
     * @link https://www.php.net/manual/pdo.prepare.php
     * @param string $statement This must be a valid SQL statement template for the target database server.
     * @param array<mixed> $options This array holds one or more key=>value pairs to set attribute values for the PDOStatement object that this method returns.
     * @return \PDOStatement|PDOStatementInterface|false
     * @phpstan-return T|false
     */
    public function prepare(string $statement, array $options = []);

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @link http://php.net/manual/pdo.query.php
     * @param string $statement The SQL statement to prepare and execute.
     * @param int|null $mode
     * @param mixed ...$fetch_mode_args
     * @return \PDOStatement|PDOStatementInterface|false Returns a PDOStatement object, or FALSE on failure.
     * @phpstan-return T|false
     */
    public function query(string $statement, ?int $mode = null, mixed ...$fetch_mode_args);

    /**
     * Quotes a string for use in a query
     *
     * @link https://www.php.net/manual/pdo.quote.php
     * @param  string $string         The string to be quoted.
     * @param  int    $type Provides a data type hint for drivers that have alternate quoting styles.
     * @return string|false Returns a quoted string that is theoretically safe to pass into an SQL statement.  Returns FALSE if the driver does not support quoting in this way.
     */
    public function quote(string $string, int $type = \PDO::PARAM_STR): string|false;

    /**
     * Rolls back a transaction
     *
     * @link https://www.php.net/manual/pdo.rollback.php
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function rollBack(): bool;

    /**
     * Set an attribute
     *
     * @link https://www.php.net/manual/pdo.setattribute.php
     * @param  int   $attribute One of the PDO::ATTR_* constants.
     * @param  mixed $value
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setAttribute(int $attribute, mixed $value): bool;
}
