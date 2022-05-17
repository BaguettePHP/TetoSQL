<?php

namespace Teto\SQL\Query;

use Teto\SQL\Query;
use Teto\SQL\DummyPDO;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectException;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectPHPException;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2019 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class ReplaceHolderTest extends \PHPUnit\Framework\TestCase
{
    use ExpectException;
    use ExpectPHPException;

    /**
     * @dataProvider acceptDataProvider
     * @param string $type
     * @param mixed $input
     * @param string|int $expected
     * @return void
     */
    public function test_accept($type, $input, $expected)
    {
        $pdo = new DummyPDO();

        $actual = call_user_func(\Closure::bind(function () use ($pdo, $type, $input) {
            return Query::replaceHolder($pdo, ':key', "@{$type}", $input, $bind_values);
        }, null, 'Teto\SQL\Query'));

        $this->assertSame($expected, $actual);
    }

    /**
     * @return iterable<array{string, mixed, string|int}>
     */
    public function acceptDataProvider()
    {
        return [
            ['ascdesc', 'ASC', 'ASC'],
            ['ascdesc', 'DESC', 'DESC'],
            ['ascdesc', 'asc', 'asc'],
            ['ascdesc', 'desc', 'desc'],
            ['int', 1, 1],
            ['int', '1', 1],
            ['int', 0, 0],
            ['int', '0', 0],
            ['int', '9223372036854775807', 9223372036854775807],
            ['int', '-9223372036854775808', (int)'-9223372036854775808'],
            ['int[]', [1, 2, 3], '1,2,3'],
            ['int[]', ['1', '2', '3'], '1,2,3'],
            ['int[]',
             ['9223372036854775807', '-9223372036854775808'],
             '9223372036854775807,-9223372036854775808',
            ],
            ['string', 0, '@0@'],
            ['string', '0', '@0@'],
            ['string', '', '@@'],
            ['string[]', ['', ''], '@@,@@'],
        ];
    }

    /**
     * @dataProvider rejeceptDataProvider
     * @param string $type
     * @param mixed $input
     * @param string $expected_message
     * @return void
     */
    public function test_raise_exception($type, $input, $expected_message)
    {
        $pdo = new DummyPDO();

        $this->expectException('DomainException');
        $this->expectExceptionMessage($expected_message);

        call_user_func(\Closure::bind(function () use ($pdo, $type, $input) {
            return Query::replaceHolder($pdo, ':key', $type, $input, $bind_values);
        }, null, 'Teto\SQL\Query'));
    }

    /**
     * @return iterable<array{string, mixed, string}>
     */
    public function rejeceptDataProvider()
    {
        return [
            ['', null, 'type specifier for param ":key" not found'],
            ['@', null, 'type specifier for param ":key" not found'],
            ['@piyo', null, 'unexpected type "@piyo"'],
            ['@ascdesc', 'foo', 'param ":key" must be "ASC", "DESC", "asc" or "desc"'],
            ['@int', '-0', 'param ":key" is unexpected integer notation'],
            ['@int', '9223372036854775808', 'param ":key" is integer out of range.'],
            ['@int', '-9223372036854775809', 'param ":key" is integer out of range.'],
            ['@int[]', 0, 'param ":key" must be int array'],
            ['@int[]', [], 'param ":key" must be not empty int array'],
            ['@int[]', ['1', 'a', '3'], 'param ":key[1]" is integer out of range.'],
            ['@string', [], 'param ":key" must be string or numeric'],
            ['@string[]', '', 'param ":key" must be string array'],
            ['@string[]', [], 'param ":key" must be not empty string array'],
            ['@string[]', ['', null], 'element of param ":key" must be string or numeric'],
        ];
    }
}
