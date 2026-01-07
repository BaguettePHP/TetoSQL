<?php

namespace Teto\SQL\Type;

use DomainException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Teto\SQL\DummyPDO;

class PgIdentifierTest extends TestCase
{
    private PgIdentifier $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new PgIdentifier([]);
    }

    /**
     * @dataProvider escapeValuesProvider
     * @phpstan-param string|array<string>|bool $input
     */
    #[DataProvider('escapeValuesProvider')]
    public function testEscapeValue(mixed $input, string $type, string $expected): void
    {
        $pdo = new DummyPDO();
        $bind_values = [];
        $this->assertSame($expected, $this->subject->escapeValue($pdo, ':key', $type, $input, $bind_values));
        $this->assertEquals([], $bind_values);
    }

    /**
     * @return array<array{string|array<?string>|bool,string,string}>
     */
    public static function escapeValuesProvider()
    {
        return [
            ['', '@column', '""'],
            ['abc', '@column', '"abc"'],
            ['ABC', '@column', '"ABC"'],
            ['ABC\\ABC\'', '@column', '"ABC\\ABC\'"'],
            ['ABC"ABC', '@column', '"ABC""ABC"'],
            ['ABC"""ABC', '@column', '"ABC""""""ABC"'],
            [['foo'], '@column[]', '"foo"'],
            [['foo', 'bar'], '@column[]', '"foo","bar"'],
            [['foo' => 'bar'], '@column[]', 'foo AS "bar"'],
            [['"foo"' => 'bar'], '@column[]', '"foo" AS "bar"'],
            [['foo' => null], '@column[]', 'foo'],
            [['"foo"' => null], '@column[]', '"foo"'],
            [['foo' => null, 'bar' => 'buz'], '@column[]', 'foo,bar AS "buz"'],
            [['"foo"' => null, 'bar' => ''], '@column[]', '"foo",bar'],
            [true, '@bool', 'TRUE'],
            [false, '@bool', 'FALSE'],
        ];
    }

    /**
     * @dataProvider escapeValuesUnexpectedValuesProvider
     * @phpstan-param string|array<string> $input
     * @phpstan-param array{class: class-string<\Exception>, message: non-empty-string} $expected
     */
    #[DataProvider('escapeValuesUnexpectedValuesProvider')]
    public function testEscapeValue_raiseError(mixed $input, string $type, array $expected): void
    {
        $this->expectException($expected['class']);
        $this->expectExceptionMessage($expected['message']);

        $pdo = new DummyPDO();
        $bind_values = [];
        $_ = $this->subject->escapeValue($pdo, ':key', $type, $input, $bind_values);
    }

    /**
     * @return array<array{mixed, string, array{class: class-string<\Exception>, message: non-empty-string}}>
     */
    public static function escapeValuesUnexpectedValuesProvider()
    {
        $DomainException = \DomainException::class;

        return [
            [1, '@bool', ['class' => $DomainException, 'message' => 'param ":key" must be bool']],
            ['', '@bool', ['class' => $DomainException, 'message' => 'param ":key" must be bool']],
            [null, '@bool', ['class' => $DomainException, 'message' => 'param ":key" must be bool']],
            ['true', '@bool', ['class' => $DomainException, 'message' => 'param ":key" must be bool']],
            [
                ['"foo"' => null, 'bar' => ''],
                '@table',
                [
                    'class' => $DomainException,
                    'message' => "Passed unexpected \$value as type '@table'. please check your query and parameters.",
                ],
            ],
        ];
    }

    /**
     * @dataProvider quoteValueProvider
     */
    #[DataProvider('quoteValueProvider')]
    public function testQuote(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->subject->quote($input));
    }

    /**
     * @return array<array{string,string}>
     */
    public static function quoteValueProvider()
    {
        return [
            ['', '""'],
            ['foo', '"foo"'],
        ];
    }
}
