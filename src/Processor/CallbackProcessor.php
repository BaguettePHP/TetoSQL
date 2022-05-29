<?php

namespace Teto\SQL\Processor;

use Closure;
use Teto\SQL\ProcessorInterface;

/**
 * Define a processor using Closure
 *
 * @phpstan-import-type teto_pdo from \Teto\SQL\PDOInterface
 */
class CallbackProcessor implements ProcessorInterface
{
    /**
     * @var Closure
     * @phpstan-var Closure(teto_pdo, string, array<non-empty-string,mixed>, array<non-empty-string,mixed>): string
     */
    protected $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    public function processQuery($pdo, $sql, array $params, array &$bind_values)
    {
        return call_user_func_array($this->callback, [$pdo, $sql, $params, &$bind_values]);
    }
}
