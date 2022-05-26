<?php

namespace Teto\SQL\Replacer;

use DomainException;
use OutOfRangeException;
use Teto\SQL\QueryBuilder;
use Teto\SQL\DummyPDO;
use Teto\SQL\Processor\PregCallbackReplacer;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectException;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectPHPException;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @author    USAMI Kenta <tadsan@zonu.me>
 * @copyright 2022 Baguette HQ
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class ForBlockTest extends TestCase
{
    use ExpectException;
    use ExpectPHPException;

    /** @var PregCallbackReplacer */
    private $subject;

    public function set_up()
    {
        $placeholder_replacer = new Placeholder();
        $this->subject = new PregCallbackReplacer([
            new ForBlock([new PregCallbackReplacer([$placeholder_replacer])])
        ]);
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
        $query_has_if = '%for :arr
  Then!
%endif
Rest';

        return [
            [
                'No collection', [], 'No collection',
            ],
            [
                '"%for :arr" in literal', [], '"%for :arr" in literal',
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
                    ]
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
                    ]
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
                    ]
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
                    ]
                ],
                '@A1@ - @B1@,@A2@ - @B2@,@A3@ - @B3@',
            ],
        ];
    }

    /**
     * @dataProvider rejeceptDataProvider
     * @param string $input
     * @phpstan-param array<non-empty-string,mixed> $params
     * @phpstan-param array{class: class-string<\Exception>, message: string} $expected
     * @return void
     */
    public function test_raise_exception($input, array $params, array $expected)
    {
        $pdo = new DummyPDO();

        $this->expectException($expected['class']);
        $this->expectExceptionMessage($expected['message']);

        $bind_values = [];
        $actual = $this->subject->processQuery($pdo, $input, $params, $bind_values);
    }

    /**
     * @return iterable<array{string, array<mixed>, array{class: class-string<\Exception>, message: string}}>
     */
    public function rejeceptDataProvider()
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
                ]
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
                    ]
                ],
                [
                    'class' => get_class(new OutOfRangeException()),
                    'message' => 'param ":b" expected but not assigned',
                ]
            ],
        ];
    }
}
