<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace Yay {

    use function Pre\Plugin\addMacro;

    // ideally this would happen automatically, but whatever
    if (function_exists('Pre\Plugin\addMacro')) {
        // Guard this so a production deploy will work properly
        addMacro(__DIR__.'/macros.yay');
    }

    function ctype(): Parser
    {
        return chain(
            llvmidentifier()->as('type'),
            optional(repeat(either(token('*'), token(\T_POW)))->as('ptr')),
            optional(repeat(chain(token('['), token(\T_LNUMBER)->as('arraySize'), token(']')))->as('array'))
        );
    }

    function llvmidentifier(): Parser
    {
        return chain(
            identifier(),
            optional(
                chain(
                    token('.'),
                    ls(
                        identifier(),
                        token('.'),
                        LS_KEEP_DELIMITER
                    )
                )
            )
        );
    }

}
namespace Yay\DSL\Expanders {

    use Yay\Token;
    use Yay\TokenStream;

    function choose(TokenStream $ts): TokenStream
    {
        $tok = $ts->current();
        $next = $ts->next();

        throw new \LogicException('blah: '.$tok->dump().' - '.$next->dump());

        return TokenStream::fromSequence(
            new Token(
                \T_CONSTANT_ENCAPSED_STRING, (string) $tok
            )
        );
    }
}
