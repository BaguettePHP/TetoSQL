<?php

namespace Teto\SQL\Processor;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Teto\SQL\DummyPDO;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2022 Baguette HQ
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class IfBlockTest extends TestCase
{
    private IfBlock $subject;

    public function setUp(): void
    {
        $this->subject = new IfBlock();
    }

    /**
     * @dataProvider acceptDataProvider
     * @param string $input
     * @phpstan-param array<non-empty-string,mixed> $params
     * @param string $expected
     */
    #[DataProvider('acceptDataProvider')]
    public function test_accept($input, array $params, $expected): void
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
    public static function acceptDataProvider()
    {
        $query_has_if = '%if :cond
  Then!
%endif
Rest';
        $query_has_if_single_line = '%if :cond Then! %endif Rest';
        $query_has_if_else = '%if :cond
  Then!
%else
  Else!
%endif
Rest';
        $query_has_if_else_single_line = '%if :cond Then! %else Else! %endif Rest';
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
                'No condition',
                [],
                'No condition',
            ],
            [
                '"%if :cond" in literal',
                [],
                '"%if :cond" in literal',
            ],
            [
                $query_has_if,
                [':cond' => true],
                "  Then!\nRest",
            ],
            [
                $query_has_if,
                [':cond' => false],
                "\nRest",
            ],
            [
                $query_has_if_single_line,
                [':cond' => false],
                ' Rest',
            ],
            [
                $query_has_if_else,
                [':cond' => false],
                "\n  Else!\nRest",
            ],
            [
                $query_has_if_else_single_line,
                [':cond' => false],
                'Else! Rest',
            ],
            [
                implode("\n", [$query_has_if_else, $query_has_if_else]),
                [':cond' => false],
                "\n  Else!\nRest\n  Else!\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => true, ':cond_2' => true],
                "  1st Then!\n\nBetween\n  2nd Then!\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => true, ':cond_2' => false],
                "  1st Then!\n\nBetween\n\n  2nd Else!\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => false, ':cond_2' => true],
                "\n  1st Else!\n\nBetween\n  2nd Then!\nRest",
            ],
            [
                $query_has_2_if_else,
                [':cond_1' => false, ':cond_2' => false],
                "\n  1st Else!\n\nBetween\n\n  2nd Else!\nRest",
            ],
            [
                $query_has_if_invalid_else,
                [':cond' => true],
                "  Then!\n\"%else\"\n  Not else!\nRest",
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
     */
    #[DataProvider('rejeceptDataProvider')]
    public function test_raise_exception($input, array $params, $expected_message): void
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
    public static function rejeceptDataProvider()
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
