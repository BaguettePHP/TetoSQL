<?php

namespace Teto\SQL\Replacer\Sample;

use Teto\SQL\TypeInterface;

class DummyType implements TypeInterface
{
    public function escapeValue($pdo, $key, $type, $value, &$bind_values)
    {
        assert(\is_string($value));
        return "[{$value}] is a dummy value.";
    }
}
