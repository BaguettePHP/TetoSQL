<?php

namespace Teto\SQL;

/**
 * Safe query builder
 *
 * @copyright 2016 pixiv Inc.
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class Query
{
    const INT64_MAX =  '9223372036854775807';
    const INT64_MIN = '-9223372036854775808';

    const RE_HOLDER = '(?<holder>(?<key>:[a-zA-Z0-9_]+)(?<type>(?:@[a-zA-Z_\[\]]+)?))';

    /**
     * Build SQL query and execute
     *
     * @param \PDO|PDOInterface|PDOAggregate $pdo
     * @param string $sql
     * @param array  $params
     */
    public static function execute($pdo, $sql, array $params)
    {
        $stmt = Query::build(($pdo instanceof PDOAggregate) ? $pdo->getPDO() : $pdo, $sql, $params);
        $stmt->execute();

        return $stmt;
    }

    /**
     * Build SQL query and execute
     *
     * @param \PDO|PDOInterface|PDOAggregate $pdo
     * @param string $sql
     * @param array  $params
     */
    public static function executeAndReturnInsertId($pdo, $sql, array $params, $name = null)
    {
        $stmt = Query::build(($pdo instanceof PDOAggregate) ? $pdo->getPDO() : $pdo, $sql, $params);
        $stmt->execute();

        return $pdo->lastInsertId($name);
    }

    /**
     * Build SQL query
     *
     * @param \PDO|PDOInterface $pdo
     * @param string $sql
     * @param array  $params
     */
    public static function build($pdo, $sql, array $params)
    {
        $bind_values = [];
        $sql = strtr($sql, "\n", ' ');
        $sql = preg_replace_callback(
            '/'.Query::RE_HOLDER.'/',
            function ($m) use ($pdo, $params, &$bind_values) {
                $key  = $m['key'];
                $type = $m['type'];

                if (!isset($params[$key])) {
                    throw new \OutOfRangeException(sprintf('param "%s" expected but not assigned', $key));
                }

                return self::replaceHolder($pdo, $key, $type, $params[$key], $bind_values);
            },
            $sql
        );

        $stmt = $pdo->prepare($sql);

        foreach ($bind_values as $key => $param) {
            list($type, $value) = $param;
            $stmt->bindParam($key, $value, $type);
        }

        return $stmt;
    }

    protected static function replaceHolder($pdo, $key, $type, $value, &$bind_values)
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
