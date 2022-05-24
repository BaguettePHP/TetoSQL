<?php

namespace Teto\SQL\Replacer;

use Teto\SQL\ProcessorInterface;

use DomainException;

/**
 * Process `%for ... %endfor` block
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class ForBlock implements ProcessorInterface
{
    const FOR_PATTERN = '/
^\s*%for\s*(?:\[(?<glue>[^\]]*)\])?\s+(?<name>:[a-zA-Z0-9_]+\s*$) # first line
(?<block>\s[\s\S]*?\s) # block, includes %else
^\s*%endfor$ # block termination
/mx';

    /** @var DynamicPlaceholder */
    private $placeholder_replacer;

    public function __construct(DynamicPlaceholder $placeholder_replacer)
    {
        $this->placeholder_replacer = $placeholder_replacer;
    }

    public function processQuery($pdo, $sql, array $params, array &$bind_values)
    {
        $built_sql = preg_replace_callback(self::FOR_PATTERN, function (array $matches) use (
            $pdo, $params, &$bind_values
        ) {
            $glue = $matches['glue'];
            $name = rtrim($matches['name']);
            if (!isset($params[$name])) {
                throw new DomainException(sprintf('Must be assigned parameter %s.', $name));
            }
            if (!is_array($params[$name])) {
                throw new DomainException(sprintf('Parameter %s must be an array.', $name));
            }

            /** @phpstan-var array<non-empty-string, array<non-empty-string, mixed>> $array */
            $array = $params[$name];

            $block = rtrim($matches['block'], "\n");
            if (strpos($block, '%for') !== false) {
                throw new DomainException('Nested %for is not supported.');
            }

            $replaced = [];
            foreach ($array as $row) {
                $replaced[] = $this->placeholder_replacer->processQuery($pdo, $block, $row, $bind_values);
            }

            return implode($glue, $replaced);
        }, $sql);

        assert($built_sql !== null);

        return $built_sql;
    }
}
