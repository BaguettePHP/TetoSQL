<?php

namespace Teto\SQL;

use Teto\SQL\Processor\PregCallbackReplacer;

/**
 * Default implementation of safer SQL query builder by TetoSQL
 *
 * @copyright 2016 pixiv Inc.
 * @license   https://github.com/BaguettePHP/TetoSQL/blob/master/LICENSE MPL-2.0
 */
final class Query extends AbstractStaticQuery
{
    /** @var QueryBuilder */
    private static $query_builder;

    public static function getQueryBuilder()
    {
        if (self::$query_builder === null) {
            $if_block= new Processor\IfBlock();

            self::$query_builder = new QueryBuilder([
                new Processor\SimpleSingleLine(),
                $if_block,
                new PregCallbackReplacer([
                    new Replacer\ForBlock([
                        new PregCallbackReplacer([
                            new Replacer\Placeholder('')
                        ])
                    ]),
                    new Replacer\Placeholder(':'),
                ]),
            ]);
        }

        return self::$query_builder;
    }
}
