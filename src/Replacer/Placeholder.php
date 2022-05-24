<?php

namespace Teto\SQL\Replacer;

use Teto\SQL\ReplacerInterface;

/**
 * Replace placeholder using {@see \PDO::quote()}
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class Placeholder implements ReplacerInterface
{
    const INT64_MAX =  '9223372036854775807';
    const INT64_MIN = '-9223372036854775808';

    const PATTERN = '(?<placeholderKey>:[a-zA-Z0-9_]+)(?<placeholderType>(?:@[a-zA-Z_\[\]]+)?)';

    public function replaceQuery($pdo, array $matches, array $params, array &$bind_values)
    {
        $key  = $matches['placeholderKey'];
        $type = $matches['placeholderType'];

        if (!isset($params[$key])) {
            throw new \OutOfRangeException(\sprintf('param "%s" expected but not assigned', $key));
        }

        return $this->replaceHolder($pdo, $key, $type, $params[$key], $bind_values);
    }

    public function getKey()
    {
        return 'placeholder';
    }

    public function getPattern()
    {
        return self::PATTERN;
    }

    /**
     * @param \PDO|\Teto\SQL\PDOInterface $pdo
     * @template S of \PDOStatement|\Teto\SQL\PDOStatementInterface
     * @phpstan-param \PDO|\Teto\SQL\PDOInterface<S> $pdo
     * @param string $key
     * @param string $type
     * @param mixed $value
     * @param ?array<mixed> $bind_values
     * @return string|int
     */
    public function replaceHolder($pdo, $key, $type, $value, &$bind_values)
    {
        if ($type === '@ascdesc') {
            if (!\in_array($value, ['ASC', 'DESC', 'asc', 'desc'], true)) {
                throw new \DomainException(\sprintf('param "%s" must be "ASC", "DESC", "asc" or "desc"', $key));
            }

            return $value;
        }

        if ($type === '@int') {
            if (\is_int($value)) {
                return $value;
            }

            if (!\is_numeric($value)) {
                throw new \DomainException(\sprintf('param "%s" must be numeric', $key));
            }

            if ($value < self::INT64_MIN || self::INT64_MAX < $value) {
                throw new \DomainException(\sprintf('param "%s" is integer out of range.', $key));
            }

            /** @var numeric-string $value */
            if ($value !== '0' && !\preg_match('/\A-?[1-9][0-9]*\z/', $value)) {
                throw new \DomainException(\sprintf('param "%s" is unexpected integer notation.', $key));
            }

            return (int)$value;
        }

        if ($type === '@int[]') {
            if (!\is_array($value)) {
                throw new \DomainException(\sprintf('param "%s" must be int array', $key));
            }
            if (\count($value) === 0) {
                throw new \DomainException(\sprintf('param "%s" must be not empty int array', $key));
            }

            foreach ($value as $i => $item) {
                $s = (string)$item;
                if ($s < self::INT64_MIN || self::INT64_MAX < $s) {
                    throw new \DomainException(\sprintf('param "%s[%d]" is integer out of range.', $key, $i));
                }
            }

            $valuesString = \implode(',', $value);
            if (\strpos(',' . $valuesString . ',', ',,') !== false) {
                throw new \LogicException('Validation Error.');
            }

            if ($value !== '0' && !\preg_match('/\A(?:-?[1-9][0-9]*)(?:,-?[1-9][0-9]*)*\z/', $valuesString)) {
                throw new \DomainException(\sprintf('param "%s" must be int array', $key));
            }

            return $valuesString;
        }

        if ($type === '@string') {
            if (!\is_string($value) && !\is_numeric($value)) {
                throw new \DomainException(\sprintf('param "%s" must be string or numeric', $key));
            }

            return $pdo->quote((string)$value, \PDO::PARAM_STR);
        }

        if ($type === '@string[]') {
            if (!\is_array($value)) {
                throw new \DomainException(\sprintf('param "%s" must be string array', $key));
            }
            if (\count($value) == 0) {
                throw new \DomainException(\sprintf('param "%s" must be not empty string array', $key));
            }
            foreach ($value as $i => $item) {
                if (!\is_string($item) && !\is_numeric($item)) {
                    throw new \DomainException(\sprintf('element of param "%s" must be string or numeric', $key));
                }
                $value[$i] = $pdo->quote((string)$item, \PDO::PARAM_STR);
            }
            return \implode(',', $value);
        }

        if ($type === '@lob') {
            if (!\is_resource($value)) {
                throw new \DomainException(\sprintf('param "%s" must be resource', $key));
            }
            $bind_values[$key] = [\PDO::PARAM_LOB, $value];

            return $key;
        }

        if ($type === '' || $type === '@') {
            throw new \DomainException(\sprintf('type specifier for param "%s" not found', $key));
        } else {
            throw new \DomainException(\sprintf('unexpected type "%s"', $type));
        }
    }

}
