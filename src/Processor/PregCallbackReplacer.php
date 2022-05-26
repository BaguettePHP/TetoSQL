<?php

namespace Teto\SQL\Processor;

use DomainException;
use LogicException;
use Teto\SQL\ProcessorInterface;
use Teto\SQL\ReplacerInterface;

/**
 * Replace the matched part with the combined PCRE regular expressions.
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class PregCallbackReplacer implements ProcessorInterface
{
    /** @phpstan-var non-empty-array<string, ReplacerInterface> */
    protected $replacers;

    /**
     * @phpstan-var non-empty-string
     */
    private $regexp;

    /**
     * @param non-empty-list<ReplacerInterface> $replacers
     */
    public function __construct(array $replacers)
    {
        $replacer_map = [];
        foreach ($replacers as $replacer) {
            $replacer_map[$replacer->getKey()] = $replacer;
        }
        $this->replacers = $replacer_map;
        $this->regexp = $this->compileRegExp($replacer_map);
    }

    public function processQuery($pdo, $sql, array $params, array &$bind_values)
    {
        $built_sql = \preg_replace_callback($this->regexp, function (array $matches) use (
            $pdo, $params, &$bind_values
        ) {
            foreach ($this->replacers as $key => $replacer) {
                if ($matches[$key] !== '') {
                    /** @phpstan-param non-empty-array<non-empty-string,string> $matches */
                    return $replacer->replaceQuery($pdo, $matches, $params, $bind_values);
                }
            }

            throw new LogicException('Did not match any replacer.');
        }, $sql);

        assert($built_sql !== null);

        return $built_sql;
    }

    /**
     * Compile regular-expression pattern
     *
     * @phpstan-param non-empty-array<non-empty-string, ReplacerInterface> $processors
     * @phpstan-return non-empty-string
     */
    public static function compileRegExp(array $processors)
    {
        $patterns = [];
        foreach ($processors as $key => $processor) {
            $patterns[] = "(?<{$key}>{$processor->getPattern()})";
        }

        return "/" . \implode('|', $patterns) . "/mx";
    }
}
