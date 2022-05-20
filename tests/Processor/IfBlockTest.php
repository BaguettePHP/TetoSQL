<?php

namespace Teto\SQL\Processor;

use Teto\SQL\QueryBuilder;
use Teto\SQL\DummyPDO;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectException;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectPHPException;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2022 Baguette HQ
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class IfBlockTest extends TestCase
{
    use ExpectException;
    use ExpectPHPException;

    /** @var IfBlock */
    private $subject;

    public function set_up()
    {
        $this->subject = new IfBlock();
    }

    /**
     * @dataProvider acceptDataProvider
     * @param string $input
     * @phpstan-param array<non-empty-string,mixed> $params
     * @param string $expected
     * @return void
     */
    public function test_accept($input, array $params, $expected)
    {
        $pdo = new DummyPDO();

        $bind_values = [];
        $actual = $this->subject->processQuery($pdo, $input, $params, $bind_values);

        $this->assertSame($expected, $actual);
        $this->assertSame([], $bind_values);
    }

    /**
     * @phpstan-return iterable<array{string, array<non-empty-string, mixed>, string}>
     */
    public function acceptDataProvider()
    {
        $query_has_if = '%if :cond
  Then!
%endif
Rest';
        $query_has_if_else = '%if :cond
  Then!
%else
  Else!
%endif
Rest';
        $query_has_2_if_else = '%if :cond_1
  1st Then!
%else
  1st Else!
%endif

Between

%if :cond_2
  2nd Then!
%else
  2nd Else!
%endif
Rest';
        $query_has_if_invalid_else = '%if :cond
  Then!
"%else"
  Not else!
%endif
Rest';

        return [
            [
                'No condition', [], 'No condition',
            ],
            [
                '"%if :cond" in literal', [], '"%if :cond" in literal',
            ],
            [
                $query_has_if,
                [':cond' => true],
                "\n  Then!\n\nRest",
            ],
            [
                $query_has_if,
                [':cond' => false],
                "\nRest",
            ],
            [
                $query_has_if_else,
                [':cond' => false],
                "\n  Else!\n\nRest",
            ],
            [
                implode("\n", [$query_has_if_else, $query_has_if_else]),
                [':cond' => false],
                "\n  Else!\n\nRest\n\n  Else!\n\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => true, ':cond_2' => true],
                "\n  1st Then!\n\n\nBetween\n\n  2nd Then!\n\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => true, ':cond_2' => false],
                "\n  1st Then!\n\n\nBetween\n\n  2nd Else!\n\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => false, ':cond_2' => true],
                "\n  1st Else!\n\n\nBetween\n\n  2nd Then!\n\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => false, ':cond_2' => false],
                "\n  1st Else!\n\n\nBetween\n\n  2nd Else!\n\nRest",
            ],
            [
                $query_has_if_invalid_else,
                [':cond' => true],
                "\n  Then!\n\"%else\"\n  Not else!\n\nRest",
            ],
            [
                $query_has_if_invalid_else,
                [':cond' => false],
                "\nRest",
            ],

        ];
    }

    /**
     * @dataProvider rejeceptDataProvider
     * @param string $input
     * @phpstan-param array<non-empty-string,mixed> $params
     * @param string $expected_message
     * @return void
     */
    public function test_raise_exception($input, array $params, $expected_message)
    {
        $pdo = new DummyPDO();

        $this->expectException('DomainException');
        $this->expectExceptionMessage($expected_message);

        $bind_values = [];
        $actual = $this->subject->processQuery($pdo, $input, $params, $bind_values);
    }

    /**
     * @return iterable<array{string, mixed, string}>
     */
    public function rejeceptDataProvider()
    {
        return [
            [
                '
%if :cond
    code
%endif',
                [],
                'Must be assigned parameter :cond',
            ],
            [
                '
%if :cond
%else
%else
%endif',
                [':cond' => true],
                'Multiple else is not allowed for %if',
            ],
        ];
    }
}
