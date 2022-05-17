<?php

namespace Teto\SQL;

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
            self::$query_builder = new QueryBuilder();
        }

        return self::$query_builder;
    }
}
