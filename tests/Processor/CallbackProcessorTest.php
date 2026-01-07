<?php

namespace Teto\SQL\Processor;

use PHPUnit\Framework\TestCase;
use Teto\SQL\DummyPDO;

class CallbackProcessorTest extends TestCase
{
    public function test(): void
    {
        $pdo = new DummyPDO();

        $bind_values = [];

        $called = false;
        $subject = new CallbackProcessor(function ($pdo, $sql, array $params, array &$bind_values) use (&$called) {
            assert($params === ['foo' => 'bar', 'buz' => 'buz']);
            $called = true;
            $bind_values = $params;
            $bind_values[] = 'Bound!';
            return 'Closure called!';
        });

        $params = ['foo' => 'bar', 'buz' => 'buz'];
        $actual = $subject->processQuery($pdo, 'before query', $params, $bind_values);

        $this->assertTrue($called);
        $this->assertEquals($bind_values, ['foo' => 'bar', 'buz' => 'buz', 'Bound!']);
        $this->assertSame($actual, 'Closure called!');
    }
}
