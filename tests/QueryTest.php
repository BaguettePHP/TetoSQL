<?php

namespace Teto\SQL;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2016 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class QueryTest extends TestCase
{
    public function test()
    {
        $pdo = new DummyPDO;
        $orig = "string: :a@string\nint :b@int\nstring: :a@string\nint :b@int\nint :c1@int\nint :c1@int";
        $stmt = Query::build($pdo, $orig, [
            ':a' => 'AAAA',
            ':b' => '2222',
            ':c1' => '0',
            ':c2' => 0,
        ]);
        $expected = 'string: @AAAA@ int 2222 string: @AAAA@ int 2222 int 0 int 0';
        $this->assertSame($expected, $stmt->queryString);
    }
}
