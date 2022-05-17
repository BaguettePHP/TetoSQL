<?php
namespace Teto\SQL;

/**
 * Interface to create an external PDO class.
 *
 * @copyright 2016 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 * @link      http://php.net/manual/class.pdo.php
 * @template S of \PDOStatement|PDOStatementInterface
 * @template T of \PDO|PDOInterface<S>
 */
interface PDOAggregate
{
    /**
     * @return \PDO|PDOInterface
     * @phpstan-return T
     */
    public function getPDO();
}
