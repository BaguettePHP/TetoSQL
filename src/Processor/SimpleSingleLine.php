<?php

namespace Teto\SQL\Processor;

use Teto\SQL\ProcessorInterface;

/**
 * Make multi-line query into single-line using {@see strtr()}
 */
class SimpleSingleLine implements ProcessorInterface
{
    public function processQuery($pdo, $sql, array $params, array &$bind_values)
    {
        return strtr($sql, "\n", ' ');
    }
}
