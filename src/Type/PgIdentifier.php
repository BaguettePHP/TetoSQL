<?php

namespace Teto\SQL\Type;

use DomainException;
use LogicException;
use Teto\SQL\TypeInterface;

/**
 * Escape identifier (database, table, field and columns names) in PostgreSQL
 *
 * Please note that this process is highly dependent on the SQL product.
 */
class PgIdentifier implements TypeInterface
{
    /** @phpstan-var array<non-empty-string,non-empty-string> */
    protected $types = [];

    /**
     * @phpstan-param array{'@column'?: non-empty-string, '@column[]'?: non-empty-string, '@table'?: non-empty-string} $type_names
     */
    public function __construct(array $type_names)
    {
        $types = [];

        foreach (['@column', '@column[]', '@table'] as $type) {
            $key = isset($type_names[$type]) ? $type_names[$type] : $type;
            $types[$key] = $type;
        }

        $this->types = $types;
    }

    public function escapeValue($pdo, $key, $type, $value, &$bind_values)
    {
        if ($type === '@bool') {
            if (\is_bool($value)) {
                return $value ? 'TRUE' : 'FALSE';
            }
            throw new DomainException(\sprintf('param "%s" must be bool', $key));
        }

        if (!isset($this->types[$type])) {
            throw new LogicException("Passed unexpected type '{$type}', please check your configuration.");
        }

        $replaced_type = $this->types[$type];
        if ($replaced_type === '@column') {
            if (\is_string($value)) {
                return $this->quote($value);
            }

            throw new DomainException("Passed unexpected \$value as type '{$type}'. please check your query and parameters.");
        }

        if ($replaced_type === '@column[]') {
            $columns = [];
            if (!\is_array($value)) {
                throw new DomainException("Passed unexpected \$value as type '{$type}'. please check your query and parameters.");
            }
            foreach ($value as $k => $v) {
                if (\is_string($k)) {
                    if ($v === null || $v === '') {
                        $columns[] = $k;
                    } else {
                        $columns[] = "{$k} AS {$this->quote($v)}";
                    }
                    continue;
                } elseif (\is_int($k)) {
                    if (\is_string($v)) {
                        $columns[] = $this->quote($v);
                        continue;
                    }
                    throw new DomainException("Passed unexpected \$value[{$k}] as type '{$type}'. please check your query and parameters.");
                }

                throw new LogicException('Unreachable');
            }

            return \implode(',', $columns);
        }

        if ($replaced_type === '@table') {
            if (\is_string($value)) {
                return $this->quote($value);
            }

            throw new DomainException("Passed unexpected \$value as type '{$type}'. please check your query and parameters.");
        }

        throw new LogicException("Unreachable, or {$type} is not implemented yet.");
    }

    /**
     * @phpstan-param string $value
     * @phpstan-return non-empty-string
     * @see https://github.com/postgres/postgres/blob/REL9_0_STABLE/src/interfaces/libpq/fe-exec.c#L3165-L3169
     * @see https://github.com/postgres/postgres/blob/REL_15_STABLE/src/interfaces/libpq/fe-exec.c#L4216-L4220
     */
    public function quote($value)
    {
        return '"' . \strtr($value, ['"' => '""']) . '"';
    }
}
