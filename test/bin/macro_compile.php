<?php

namespace Pre\Plugin {
    function defer($code) {
        $hidden = find("hidden/vendor/autoload.php");
        $visible = find("autoload.php");
        if (!$visible) {
            // the plugin is being used/tested directly
            $visible = __DIR__ . "/../vendor/autoload.php";
        }
        $defer = "
            namespace Yay {
                /**
                 * Abusing namespaces to make Cycle->id() predictable during tests only!
                 */
                function md5(\$foo) { return (string) \$foo; }
            }
            namespace {
                require '{$hidden}';
                require '{$visible}';
                \$function = function() {
                    {$code};
                };
                print base64_encode(gzencode(\$function()));
            }
        ";
        $result = exec(
            escapeshellcmd(PHP_BINARY) . " -r 'eval(base64_decode(\"" . base64_encode($defer) . "\"));'",
            $output
        );

        $value = @gzdecode(base64_decode($result));
        if (!$value) {
            throw new \LogicException("Test failed due to: \n" . implode("\n", $output));
        }
        return $value;
    }
}

namespace {
    require __DIR__ . '/../../vendor/autoload.php';

    use function Pre\Plugin\instance;

    $code = stream_get_contents(STDIN);

    echo instance()->parse($code);
}