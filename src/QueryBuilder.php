<?php

namespace Teto\SQL;

/**
 * A generic SQL template engine (dynamic placeholder)
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class QueryBuilder
{
    const INT64_MAX =  '9223372036854775807';
    const INT64_MIN = '-9223372036854775808';

    const RE_HOLDER = '(?<holder>(?<key>:[a-zA-Z0-9_]+)(?<type>(?:@[a-zA-Z_\[\]]+)?))';

    /**
     * Build SQL query
     *
     * @template S of \PDOStatement|PDOStatementInterface
     * @template T of \PDO|PDOInterface<S>
     * @param \PDO|PDOInterface $pdo
     * @phpstan-param T $pdo
     * @phpstan-param non-empty-string $sql
     * @phpstan-param array<non-empty-string,mixed> $params
     * @return \PDOStatement
     * @phpstan-return ($pdo is \PDO ? \PDOStatement : S)
     */
    public function build($pdo, $sql, array $params)
    {
        $bind_values = [];
        $built_sql = $this->replaceParameters($pdo, $this->trimQuery($sql), $params, $bind_values);

        $stmt = $pdo->prepare($built_sql);
        assert($stmt !== false);

        foreach ($bind_values as $key => $param) {
            list($type, $value) = $param;
            $stmt->bindParam($key, $value, $type);
        }

        return $stmt;
    }

    /**
     * @param string $sql
     * @phpstan-param non-empty-string $sql
     * @return string
     */
    public function trimQuery($sql)
    {
        return strtr($sql, "\n", ' ');
    }

    /**
     * Replace query placeholders with parameters
     *
     * @template S of \PDOStatement|PDOStatementInterface
     * @template T of \PDO|PDOInterface<S>
     * @param \PDO|PDOInterface $pdo
     * @phpstan-param T $pdo
     * @param string $sql
     * @phpstan-param array<non-empty-string,mixed> $params
     * @phpstan-param array<non-empty-string,mixed> $bind_values
     * @return string
     */
    public function replaceParameters($pdo, $sql, array $params, array &$bind_values)
    {
        /** @var string $built_sql */
        $built_sql = preg_replace_callback(
            '/' . self::RE_HOLDER . '/',
            function ($m) use ($pdo, $params, &$bind_values) { // @phpstan-ignore-line
                $key  = $m['key'];
                $type = $m['type'];

                if (!isset($params[$key])) {
                    throw new \OutOfRangeException(sprintf('param "%s" expected but not assigned', $key));
                }

                return $this->replaceHolder($pdo, $key, $type, $params[$key], $bind_values);
            },
            $sql
        );

        return $built_sql;
    }

    /**
     * @param \PDO|PDOInterface $pdo
     * @template S of \PDOStatement|PDOStatementInterface
     * @phpstan-param \PDO|PDOInterface<S> $pdo
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param ?array<mixed> $bind_values
     * @return string|int
     */
    public function replaceHolder($pdo, $key, $type, $value, &$bind_values)
    {
        if ($type === '@ascdesc') {
            if (!in_array($value, ['ASC', 'DESC', 'asc', 'desc'], true)) {
                throw new \DomainException(sprintf('param "%s" must be "ASC", "DESC", "asc" or "desc"', $key));
            }

            return $value;
        }

        if ($type === '@int') {
            if (is_int($value)) {
                return $value;
            }

            if (!is_numeric($value)) {
                throw new \DomainException(sprintf('param "%s" must be numeric', $key));
            }

            if ($value < self::INT64_MIN || self::INT64_MAX < $value) {
                throw new \DomainException(sprintf('param "%s" is integer out of range.', $key));
            }

            /** @var numeric-string $value */
            if ($value !== '0' && !preg_match('/\A-?[1-9][0-9]*\z/', $value)) {
                throw new \DomainException(sprintf('param "%s" is unexpected integer notation.', $key));
            }

            return (int)$value;
        }

        if ($type === '@int[]') {
            if (!is_array($value)) {
                throw new \DomainException(sprintf('param "%s" must be int array', $key));
            }
            if (count($value) === 0) {
                throw new \DomainException(sprintf('param "%s" must be not empty int array', $key));
            }

            foreach ($value as $i => $item) {
                $s = (string)$item;
                if ($s < self::INT64_MIN || self::INT64_MAX < $s) {
                    throw new \DomainException(sprintf('param "%s[%d]" is integer out of range.', $key, $i));
                }
            }

            $valuesString = implode(',', $value);
            if (strpos(',' . $valuesString . ',', ',,') !== false) {
                throw new \LogicException('Validation Error.');
            }

            if ($value !== '0' && !preg_match('/\A(?:-?[1-9][0-9]*)(?:,-?[1-9][0-9]*)*\z/', $valuesString)) {
                throw new \DomainException(sprintf('param "%s" must be int array', $key));
            }

            return $valuesString;
        }

        if ($type === '@string') {
            if (!is_string($value) && !is_numeric($value)) {
                throw new \DomainException(sprintf('param "%s" must be string or numeric', $key));
            }

            return $pdo->quote((string)$value, \PDO::PARAM_STR);
        }

        if ($type === '@string[]') {
            if (!is_array($value)) {
                throw new \DomainException(sprintf('param "%s" must be string array', $key));
            }
            if (count($value) == 0) {
                throw new \DomainException(sprintf('param "%s" must be not empty string array', $key));
            }
            foreach ($value as $i => $item) {
                if (!is_string($item) && !is_numeric($item)) {
                    throw new \DomainException(sprintf('element of param "%s" must be string or numeric', $key));
                }
                $value[$i] = $pdo->quote((string)$item, \PDO::PARAM_STR);
            }
            return implode(',', $value);
        }

        if ($type === '@lob') {
            if (!is_resource($value)) {
                throw new \DomainException(sprintf('param "%s" must be resource', $key));
            }
            $bind_values[$key] = [\PDO::PARAM_LOB, $value];

            return $key;
        }

        if ($type === '' || $type === '@') {
            throw new \DomainException(sprintf('type specifier for param "%s" not found', $key));
        } else {
            throw new \DomainException(sprintf('unexpected type "%s"', $type));
        }
    }
}
