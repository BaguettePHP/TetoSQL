<?php

namespace Teto\SQL\Replacer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Teto\SQL\DummyPDO;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2019 USAMI Kenta
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class PlaceholderTest extends TestCase
{
    private Placeholder $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new Placeholder(':', [
            '@dummy' => new Sample\DummyType(),
        ]);
    }

    /**
     * @dataProvider acceptDataProvider
     */
    #[DataProvider('acceptDataProvider')]
    public function test_accept(string $type, mixed $input, string|int $expected): void
    {
        $pdo = new DummyPDO();

        $actual = $this->subject->replaceHolder($pdo, ':key', "@{$type}", $input, $bind_values);

        ;

        $this->assertSame($expected, $actual);
    }

    /**
     * @return iterable<array{string, mixed, string|int}>
     */
    public static function acceptDataProvider()
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
            ['int', '-9223372036854775808', (int) '-9223372036854775808'],
            ['int[]', [0], '0'],
            ['int[]', ['0'], '0'],
            ['int[]', [0, 0], '0,0'],
            ['int[]', ['0', '0'], '0,0'],
            ['int[]', [1, 2, 3], '1,2,3'],
            ['int[]', ['1', '2', '3'], '1,2,3'],
            ['int[]', [0, 1, 2, 3], '0,1,2,3'],
            ['int[]', ['0', '1', '2', '3'], '0,1,2,3'],
            ['int[]', [1, 0, 2, 3], '1,0,2,3'],
            ['int[]', ['1', '0', '2', '3'], '1,0,2,3'],
            ['int[]', [1, 2, 0, 3], '1,2,0,3'],
            ['int[]', ['1', '2', '0', '3'], '1,2,0,3'],
            ['int[]', [1, 2, 3, 0], '1,2,3,0'],
            ['int[]', ['1', '2', '3', '0'], '1,2,3,0'],
            ['int[]', ['9223372036854775807', '-9223372036854775808'], '9223372036854775807,-9223372036854775808'],
            ['string', 0, '@0@'],
            ['string', '0', '@0@'],
            ['string', '', '@@'],
            ['string[]', ['', ''], '@@,@@'],
            ['dummy', 'foo', '[foo] is a dummy value.'],
        ];
    }

    /**
     * @dataProvider rejeceptDataProvider
     * @param string $type
     * @param mixed $input
     * @param string $expected_message
     */
    #[DataProvider('rejeceptDataProvider')]
    public function test_raise_exception(string $type, mixed $input, string $expected_message): void
    {
        $pdo = new DummyPDO();

        $this->expectException('DomainException');
        $this->expectExceptionMessage($expected_message);

        $this->subject->replaceHolder($pdo, ':key', $type, $input, $bind_values);
    }

    /**
     * @return iterable<array{string, mixed, string}>
     */
    public static function rejeceptDataProvider()
    {
        return [
            ['', null, 'type specifier for param ":key" not found'],
            ['@', null, 'type specifier for param ":key" not found'],
            ['@piyo', null, 'unexpected type "@piyo"'],
            ['@ascdesc', 'foo', 'param ":key" must be "ASC", "DESC", "asc" or "desc"'],
            ['@int', '-0', 'param ":key" is unexpected integer notation'],
            ['@int', '00', 'param ":key" is unexpected integer notation'],
            ['@int', '9223372036854775808', 'param ":key" is integer out of range.'],
            ['@int', '-9223372036854775809', 'param ":key" is integer out of range.'],
            ['@int[]', 0, 'param ":key" must be int array'],
            ['@int[]', [], 'param ":key" must be not empty int array'],
            ['@int[]', ['1', 'a', '3'], 'param ":key[1]" is integer out of range.'],
            ['@int[]', ['00'], 'param ":key" must be int array'],
            ['@int[]', ['9223372036854775808'], 'param ":key[0]" is integer out of range.'],
            ['@int[]', ['-9223372036854775809'], 'param ":key[0]" is integer out of range.'],
            ['@string', [], 'param ":key" must be string or numeric'],
            ['@string[]', '', 'param ":key" must be string array'],
            ['@string[]', [], 'param ":key" must be not empty string array'],
            ['@string[]', ['', null], 'element of param ":key" must be string or numeric'],
        ];
    }
}
