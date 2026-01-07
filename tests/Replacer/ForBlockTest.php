<?php

namespace Teto\SQL\Replacer;

use DomainException;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Teto\SQL\DummyPDO;
use Teto\SQL\Processor\PregCallbackReplacer;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2022 Baguette HQ
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class ForBlockTest extends TestCase
{
    private PregCallbackReplacer $subject;

    public function setUp(): void
    {
        parent::setUp();

        $placeholder_replacer = new Placeholder();
        $this->subject = new PregCallbackReplacer([
            new ForBlock([new PregCallbackReplacer([$placeholder_replacer])]),
        ]);
    }

    /**
     * @dataProvider acceptDataProvider
     * @phpstan-param array<non-empty-string,mixed> $params
     */
    #[DataProvider('acceptDataProvider')]
    public function test_accept(string $input, array $params, string $expected): void
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
        $query_has_if = '%for :arr
  Then!
%endif
Rest';

        return [
            [
                'No collection',
                [],
                'No collection',
            ],
            [
                '"%for :arr" in literal',
                [],
                '"%for :arr" in literal',
            ],
            [
                '%for :arr
    :a@string - :b@string
%endfor',
                [
                    ':arr' => [
                        [':a' => 'A1', ':b' => 'B1'],
                        [':a' => 'A2', ':b' => 'B2'],
                        [':a' => 'A3', ':b' => 'B3'],
                    ],
                ],
                '@A1@ - @B1@,@A2@ - @B2@,@A3@ - @B3@',
            ],
            [
                '%for :arr  :a@string - :b@string  %endfor',
                [
                    ':arr' => [
                        [':a' => 'A1', ':b' => 'B1'],
                        [':a' => 'A2', ':b' => 'B2'],
                        [':a' => 'A3', ':b' => 'B3'],
                    ],
                ],
                '@A1@ - @B1@,@A2@ - @B2@,@A3@ - @B3@',
            ],
            [
                '%for[] :arr  :a@string - :b@string  %endfor ',
                [
                    ':arr' => [
                        [':a' => 'A1', ':b' => 'B1'],
                        [':a' => 'A2', ':b' => 'B2'],
                        [':a' => 'A3', ':b' => 'B3'],
                    ],
                ],
                '@A1@ - @B1@,@A2@ - @B2@,@A3@ - @B3@',
            ],
            [
                '%for[,] :arr  :a@string - :b@string  %endfor ',
                [
                    ':arr' => [
                        [':a' => 'A1', ':b' => 'B1'],
                        [':a' => 'A2', ':b' => 'B2'],
                        [':a' => 'A3', ':b' => 'B3'],
                    ],
                ],
                '@A1@ - @B1@,@A2@ - @B2@,@A3@ - @B3@',
            ],
        ];
    }

    /**
     * @dataProvider rejeceptDataProvider
     * @phpstan-param array<non-empty-string,mixed> $params
     * @phpstan-param array{class: class-string<\Exception>, message: string} $expected
     */
    #[DataProvider('rejeceptDataProvider')]
    public function test_raise_exception(string $input, array $params, array $expected): void
    {
        $pdo = new DummyPDO();

        $this->expectException($expected['class']);
        $this->expectExceptionMessage($expected['message']);

        $bind_values = [];
        $_ = $this->subject->processQuery($pdo, $input, $params, $bind_values);
    }

    /**
     * @return iterable<array{string, array<mixed>, array{class: class-string<\Exception>, message: string}}>
     */
    public static function rejeceptDataProvider()
    {
        return [
            [
                '%for :arr
    :a@string - :b@string
%endfor',
                [],
                [
                    'class' => get_class(new DomainException()),
                    'message' => 'Must be assigned parameter :arr.',
                ],
            ],
            [
                '%for :arr
    :a@string - :b@string
%endfor',
                [
                    ':arr' => [
                        [':a' => 'A1', ':b' => 'B1'],
                        [':a' => 'A2'],
                        [':a' => 'A3', ':b' => 'B3'],
                    ],
                ],
                [
                    'class' => get_class(new OutOfRangeException()),
                    'message' => 'param ":b" expected but not assigned',
                ],
            ],
        ];
    }
}
