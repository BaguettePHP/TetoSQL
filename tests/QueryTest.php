<?php
namespace Teto\SQL;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2016 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $pdo = new DummyPDO;
        $orig = "string: :a@string\nint :b@int\nstring: :a@string\nint :b@int";
        $stmt = Query::build($pdo, $orig, [
            ':a' => 'AAAA',
            ':b' => '2222',
        ]);
        $expected = 'string: @AAAA@ int 2222 string: @AAAA@ int 2222';
        $this->assertSame($expected, $stmt->queryString);
    }
}
