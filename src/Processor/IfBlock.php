<?php

namespace Teto\SQL\Processor;

use Teto\SQL\ProcessorInterface;

use DomainException;

/**
 * Process `%if ... %endif` block
 *
 * @copyright 2016 pixiv Inc.
 * @license https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
class IfBlock implements ProcessorInterface
{
    const IF_PATTERN = '/
(?:^|\s)%if\s+(?<cond>:[a-zA-Z0-9_]+\s) # first line
(?<block>[\s\S]*?) # block, includes %else
\s%endif\b # block termination
/mx';
    const ELSE_SPLITTER = '/(?:^|\s)%else(?:$|\s)/m';

    public function processQuery($pdo, $sql, array $params, array &$bind_values)
    {
        $built_sql = preg_replace_callback(self::IF_PATTERN, function (array $matches) use ($params) {
            $cond = rtrim($matches['cond']);
            if (!isset($params[$cond])) {
                throw new DomainException(sprintf('Must be assigned parameter %s.', $cond));
            }

            $block = $matches['block'];
            if (strpos($block, '%if') !== false) {
                throw new DomainException('Nested %if is not supported.');
            }

            $blocks = preg_split(self::ELSE_SPLITTER, $block) ?: [$block, ' '];
            if (count($blocks) > 2) {
                throw new DomainException('Multiple else is not allowed for %if.');
            }

            if ($params[$cond]) {
                return $blocks[0];
            }

            return isset($blocks[1]) ? $blocks[1] : '';
        }, $sql);

        assert($built_sql !== null);

        return $built_sql;
    }
}
