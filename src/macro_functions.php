<?php

namespace Yay;

use function Pre\Plugin\addMacro;

// ideally this would happen automatically, but whatever
addMacro(__DIR__ . '/macros.yay');

function ctype(): Parser
{
    return chain(
        label()->as('type'),
        optional(repeat(rtoken('(\\*)'))->as('ptr'))
    );
}