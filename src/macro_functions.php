<?php

namespace Yay {

    use function Pre\Plugin\addMacro;

    // ideally this would happen automatically, but whatever
    addMacro(__DIR__ . '/macros.yay');

    function ctype(): Parser
    {
        return chain(
            llvmidentifier()->as('type'),
            optional(repeat(token('*'))->as('ptr'))
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

    function choose(TokenStream $ts): TokenStream {
        $tok = $ts->current();
        $next = $ts->next();
        throw new \LogicException("blah: " . $tok->dump() . ' - ' . $next->dump());
        return TokenStream::fromSequence(
            new Token(
                T_CONSTANT_ENCAPSED_STRING, (string) $tok
            )
        );
    }
}
