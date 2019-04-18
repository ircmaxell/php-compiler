<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace Yay {
    /**
     * Abusing namespaces to make Cycle->id() predictable during tests only!
     */
    function md5($foo)
    {
        return (string) $foo;
    }
}
namespace {
    require __DIR__.'/../../vendor/autoload.php';

    use function Pre\Plugin\instance;

    $code = stream_get_contents(\STDIN);

    echo instance()->parse($code);
}
