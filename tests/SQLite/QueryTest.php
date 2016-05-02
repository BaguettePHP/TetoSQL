<?php
namespace Teto\SQL\SQLite;
use Teto\SQL\Query;

final class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PDO */
    private $pdo;

    public function setUp()
    {
        $pdo = $this->getPDO();
        $pdo->exec(self::DROP_TABLE);
        $pdo->exec(self::CREATE_TABLE);
    }

    const DROP_TABLE = 'DROP TABLE IF EXISTS `books`';
    const CREATE_TABLE = '
CREATE TABLE `books` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name`  TEXT,
    `cover` BLOB
)
';
    const INSERT = 'INSERT INTO `books` (`name`, `cover`) VALUES (:name@string, :cover@lob)';
    const SELECT_BY_ID = 'SELECT `id`, `name`, `cover` FROM `books` WHERE `id` = :id@int';

    public function getPDO()
    {
        if (is_null($this->pdo)) {
            $dsn = 'sqlite:/' . __DIR__ . '/db.sq3';
            $this->pdo = new \PDO($dsn, null, null, [\PDO::ATTR_PERSISTENT => true]);
        }

        return $this->pdo;
    }

    public function test()
    {
        $pdo = $this->getPDO();

        $img_file = dirname(__DIR__) . '/fuji36_01.jpg';
        $id = Query::executeAndReturnInsertId($pdo, self::INSERT, [
            ':name'  => 'Thirty-six Views of Mount Fuji',
            ':cover' => fopen($img_file, 'rb'),
        ]);

        $actual = Query::execute($pdo, self::SELECT_BY_ID, [
            ':id' => $id,
        ])->fetch(\PDO::FETCH_ASSOC);

        $this->assertSame($id, $actual['id']);
        $this->assertSame('Thirty-six Views of Mount Fuji', $actual['name']);

        $blob = file_get_contents($img_file);
        $this->assertTrue($blob === $actual['cover']);
    }
}
