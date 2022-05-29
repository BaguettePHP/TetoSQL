<?php

namespace Teto\SQL\Type;

use Teto\SQL\DummyPDO;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class PgIdentifierTest extends TestCase
{
    /** @var PgIdentifier */
    private $subject;

    public function set_up()
    {
        parent::set_up();

        $this->subject = new PgIdentifier([]);
    }

    /**
     * @dataProvider escapeValuesProvider
     * @phpstan-param string|array<string> $input
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
     * @return array<array{string|array<?string>,string,string}>
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
