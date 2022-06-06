<?php

namespace Teto\SQL\Type;

use DomainException;
use Teto\SQL\DummyPDO;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class PgIdentifierTest extends TestCase
{
    /** @var PgIdentifier */
    private $subject;

    public function set_up()
    {
        /** @noinspection PhpMultipleClassDeclarationsInspection */
        parent::set_up();

        $this->subject = new PgIdentifier([]);
    }

    /**
     * @dataProvider escapeValuesProvider
     * @phpstan-param string|array<string>|bool $input
     * @param string $type
     * @param string $expected
     * @return void
     */
    public function testEscapeValue($input, $type, $expected)
    {
        $pdo = new DummyPDO();
        $bind_values = [];
        $this->assertSame($expected, $this->subject->escapeValue($pdo, ':key', $type,  $input, $bind_values));
        $this->assertEquals([], $bind_values);
    }

    /**
     * @return array<array{string|array<?string>|bool,string,string}>
     */
    public function escapeValuesProvider()
    {
        return [
            ['' , '@column', '""'],
            [['foo'] , '@column[]', '"foo"'],
            [['foo','bar'] , '@column[]', '"foo","bar"'],
            [['foo' => 'bar'] , '@column[]', 'foo AS "bar"'],
            [['"foo"' => 'bar'] , '@column[]', '"foo" AS "bar"'],
            [['foo' => null] , '@column[]', 'foo'],
            [['"foo"' => null] , '@column[]', '"foo"'],
            [['foo' => null, 'bar' => 'buz'] , '@column[]', 'foo,bar AS "buz"'],
            [['"foo"' => null, 'bar' => ''] , '@column[]', '"foo",bar'],
            [true, '@bool', 'TRUE'],
            [false, '@bool', 'FALSE'],
        ];
    }

    /**
     * @dataProvider escapeValuesUnexpectedValuesProvider
     * @phpstan-param string|array<string> $input
     * @param string $type
     * @phpstan-param array{class: class-string<\Exception>, message: non-empty-string} $expected
     * @return void
     */
    public function testEscapeValue_raiseError($input, $type, array $expected)
    {
        $this->expectException($expected['class']);
        $this->expectExceptionMessage($expected['message']);

        $pdo = new DummyPDO();
        $bind_values = [];
        $_ = $this->subject->escapeValue($pdo, ':key', $type,  $input, $bind_values);
    }

    /**
     * @return array<array{mixed, string, array{class: class-string<\Exception>, message: non-empty-string}}>
     */
    public function escapeValuesUnexpectedValuesProvider()
    {
        /** @phpstan-var class-string<DomainException> $DomainException */
        $DomainException = 'DomainException';

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
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testQuote($input, $expected)
    {
        $this->assertSame($expected, $this->subject->quote($input));
    }

    /**
     * @return array<array{string,string}>
     */
    public function quoteValueProvider()
    {
        return [
            ['' , '""'],
            ['foo' , '"foo"'],
        ];
    }
}
