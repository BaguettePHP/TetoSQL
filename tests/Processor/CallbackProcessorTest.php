<?php

namespace Teto\SQL\Processor;

use Teto\SQL\DummyPDO;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class CallbackProcessorTest extends TestCase
{
    /**
     * @return void
     */
    public function test()
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
