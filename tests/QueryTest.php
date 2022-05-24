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
    /**
     * @dataProvider queryProvider
     * @param non-empty-string $query
     * @param array<non-empty-string, mixed> $params
     * @param string $expected
     * @return void
     */
    public function test($query, array $params, $expected)
    {
        $pdo = new DummyPDO();
        $stmt = Query::build($pdo, $query, $params);

        $this->assertSame($expected, $stmt->queryString);
    }

    /**
     * @return array<array{string, array<string, mixed>, string}>
     */
    public function queryProvider()
    {
        return [
            [
                "string: :a@string\nint :b@int\nstring: :a@string\nint :b@int\nint :c1@int\nint :c1@int",
                [
                    ':a' => 'AAAA',
                    ':b' => '2222',
                    ':c1' => '0',
                    ':c2' => 0,
                ],
                'string: @AAAA@ int 2222 string: @AAAA@ int 2222 int 0 int 0',
            ],
            [
                <<<'SQL'
SELECT foo, bar, buz
FROM hoge
WHERE id = :id@int
SQL
,
                [
                    ':id' => 12345,
                ],
                'SELECT foo, bar, buz FROM hoge WHERE id = 12345',
            ],
            [
                <<<'SQL'
SELECT foo, bar, buz
FROM hoge
WHERE id = :id@int
%if :order
  ORDER BY id ASC
%endif
SQL
,
                [
                    ':id' => 12345,
                    ':order' => true,
                ],
                'SELECT foo, bar, buz FROM hoge WHERE id = 12345  ORDER BY id ASC',
            ],
            [
                <<<'SQL'
SELECT foo, bar, buz
FROM hoge
WHERE id = :id@int
%if :order
  ORDER BY id ASC
%endif
SQL
,
                [
                    ':id' => 12345,
                    ':order' => false,
                ],
                'SELECT foo, bar, buz FROM hoge WHERE id = 12345',
            ],
            [
                <<<'SQL'
SELECT foo, bar, buz
FROM hoge
WHERE id IN (:ids@int[])
%if :order
  ORDER BY id ASC
%endif
SQL
,
                [
                    ':ids' => [12345, 23456, 78901],
                    ':order' => false,
                ],
                'SELECT foo, bar, buz FROM hoge WHERE id IN (12345,23456,78901)',
            ],
            [
                <<<'SQL'
INSERT INTO hoge
VALUES
%for[,] :values
  (:id@int, :name@string)
%endfor
SQL
,
                [
                    ':id' => 12345,
                    ':values' => [
                        [':id' => 0, ':name' => 'hoge'],
                        [':id' => 1, ':name' => 'fuga'],
                        [':id' => 2, ':name' => 'piyo'],
                    ],
                ],
                'INSERT INTO hoge VALUES(0, @hoge@),(1, @fuga@),(2, @piyo@)',
            ],
        ];
    }
}
