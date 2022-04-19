TetoSQL
=======

[![Test](https://github.com/BaguettePHP/TetoSQL/actions/workflows/test.yml/badge.svg)](https://github.com/BaguettePHP/TetoSQL/actions/workflows/test.yml)

[PHP Data Objects](http://php.net/manual/book.pdo.php)(PDO) wrapper and SQL Template for PHP

Features
--------

- PDO Wrapper
  - [BLOB support](http://php.net/manual/pdo.lobs.php)
- Query Template
  - Type safe
  - Sequence of values

Manual
------

Japanese: [憂鬱なSQLのためのアレ、またはPDOと仲良くして枕を高くしてねむる](http://qiita.com/tadsan/items/e615a779baa6eabdab47)

Syntax
------

### type

* `@int` - Integer value (`-9223372036854775808 <= n <=9223372036854775807`)
* `@int[]` - Sequence of integers
* `@string` - String
* `@string[]` - Sequence of strings
* `@lob` - [Large OBject](http://php.net/manual/pdo.lobs.php)
* `@ascdesc` - `"ASC"` or `"DESC"` or `"asc"` or `"desc"`

### Example

``` php
<?php
namespace MyApp;
use Teto\SQL;

const find = '
 SELECT * FROM `users`
 WHERE `status` IN (:statuses@int[])
 LIMIT :offset@int, :limit@int
';
$conn = new \PDO('sqlite:/tmp/db.sq3', null, null, [PDO::ATTR_PERSISTENT => true]);
$data = Query::execute($conn, find, [
    ':statuses' => [1, 3],
    ':offset'   => 60,
    ':limit'    => 30,
])->fetch(\PDO::FETCH_ASSOC);
```

Copyright
---------

**TetoSQL** is licensed under [Mozilla Public License Version 2.0](https://www.mozilla.org/en-US/MPL/2.0/).

    Simple and secure SQL templating
    Copyright (c) 2019 USAMI Kenta <tadsan@zonu.me>

### PxvSql

**TetoSQL** is forked (*and detuned*) from private library of [pixiv Inc.](http://www.pixiv.co.jp/) that is called `PxvSql`.

### PHP Manual

[`PDOInterface.php`](http://php.net/manual/en/class.pdo.php) and [`PDOStatementInterface.php`](http://php.net/manual/en/class.pdostatement.php) is based on [PHP Manual (en)](http://php.net/manual/en/index.php).

> Copyright © 1997 - 2016 by the PHP Documentation Group. This material may be distributed only subject to the terms and conditions set forth in the Creative Commons Attribution 3.0 License or later. A copy of the [Creative Commons Attribution 3.0 license](http://php.net/manual/en/copyright.php) is distributed with this manual. The latest version is presently available at [» http://creativecommons.org/licenses/by/3.0/](http://creativecommons.org/licenses/by/3.0/).
