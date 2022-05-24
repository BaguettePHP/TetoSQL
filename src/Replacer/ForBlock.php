<?php

namespace Teto\SQL\Replacer;

use Teto\SQL\ProcessorInterface;
use Teto\SQL\ReplacerInterface;

use DomainException;

/**
 * Process `%for ... %endfor` block
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class ForBlock implements ReplacerInterface
{
    const FOR_PATTERN = '(?:^|\s)%for\s*(?:\[(?<forGlue>[^\]]*)\])?\s+(?<forName>:[a-zA-Z0-9_]+\s) # first line
(?<forBlock>\s[\s\S]*?)\s # block, includes %else
(?:^|\s*)%endfor(?:\s|$) # block termination
';

    /** @phpstan-var list<ProcessorInterface> */
    private $processors;

    /**
     * @phpstan-param list<ProcessorInterface> $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    public function getKey()
    {
        return 'for';
    }

    public function getPattern()
    {
        return self::FOR_PATTERN;
    }

    public function replaceQuery($pdo, array $matches, array $params, array &$bind_values)
    {
        $glue = $matches['forGlue'];
        if ($glue === '') {
            $glue = ',';
        }
        $name = rtrim($matches['forName']);
        if (!isset($params[$name])) {
            throw new DomainException(sprintf('Must be assigned parameter %s.', $name));
        }
        if (!is_array($params[$name])) {
            throw new DomainException(sprintf('Parameter %s must be an array.', $name));
        }

        /** @phpstan-var array<non-empty-string, array<non-empty-string, mixed>> $array */
        $array = $params[$name];

        $block = $matches['forBlock'];
        if (strpos($block, '%for') !== false) {
            throw new DomainException('Nested %for is not supported.');
        }

        $replaced = [];
        foreach ($array as $row) {
            $new = $block;
            foreach ($this->processors as $processor) {
                $new = $processor->processQuery($pdo, $new, $row, $bind_values);
            }
            $replaced[] = \ltrim($new);
        }

        return implode($glue, $replaced);
    }
}
