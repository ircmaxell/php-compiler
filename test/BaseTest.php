<?php

namespace PHPCompiler;

use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase {

    protected string $BIN = '';

    const EXPECTATIONS = [
        'EXPECT',
        'EXPECTF',
        'EXPECTREGEX',
    ];

    const EXTERNAL_SECTIONS = [
        'FILE',
        'EXPECT',
        'EXPECTF',
        'EXPECTREGEX',
    ];

    const REQUIRED_SECTIONS = [
        'FILE',
        self::EXPECTATIONS,
    ];

    const UNSUPPORTED_SECTIONS = [
        'REDIRECTTEST',
        'REQUEST',
        'POST',
        'PUT',
        'POST_RAW',
        'GZIP_POST',
        'DEFLATE_POST',
        'GET',
        'COOKIE',
        'HEADERS',
        'CGI',
        'EXPECTHEADERS',
        'EXTENSIONS',
        'PHPDBG',
    ];

    public static function providePHPTests(): \Generator {
        yield from self::providePHPTestsFromDir(__DIR__ . '/cases');
    }

    private static function providePHPTestsFromDir(string $dir): \Generator {
        foreach (new \DirectoryIterator($dir) as $path) {
            if (!$path->isDir() || $path->isDot()) {
                continue;
            }
            yield from self::providePHPTestsFromDir($path->getPathname());
        }
        foreach (new \GlobIterator($dir . '/*.phpt') as $test) {
            yield self::parsePHPT($test->getPathname(), $test->getBasename());
        }
    }

    private static function parsePHPT(string $filename, string $basename): array {
        $sections = [];
        $section = '';
        foreach (file($filename) as $line) {
            if (preg_match('(^--([_A-Z]+)--)', $line, $result)) {
                $section = $result[1];
                $sections[$section] = '';
                continue;
            }
            if (empty($section)) {
                throw new \LogicException("Invalid PHPT file: empty section header");
            }
            $sections[$section] .= $line;
        }
        if (!isset($sections['TEST'])) {
            throw new \LogicException("Every test must have a name");
        }
        if (isset($sections['FILEEOF'])) {
            $sections['FILE'] = rtrim($sections['FILEEOF'], "\r\n");
            unset($sections['FILEEOF']);
        }
        self::parseExternal($sections, dirname($filename));
        if (!self::validate($sections)) {
            throw new \LogicException("Invalid PHPT File");
        }
        foreach (self::UNSUPPORTED_SECTIONS as $section) {
            if (isset($sections[$section])) {
                throw new \LogicException("PHPT $section sections are not supported");
            }
        }
        return [
            trim($sections["TEST"]),
            $sections['FILE'],
            $sections,
        ];
    }

    private static function parseExternal(array &$sections, string $testdir): void {
        foreach (self::EXTERNAL_SECTIONS as $section) {
            if (isset($sections[$section . '_EXTERNAL'])) {
                $filename = trim($sections[$section . '_EXTERNAL']);
                if (!is_file($testdir . '/' . $filename)) {
                    throw new \RuntimeException("Could not load external file $filename");
                }
                $sections[$section] = file_get_contents($testdir . '/' . $filename);
            }
        }
    }

    private static function validate(array &$sections): bool {
        foreach (self::REQUIRED_SECTIONS as $section) {
            if (is_array($section)) {
                foreach ($section as $any) {
                    if (isset($sections[$any])) {
                        continue 2;
                    }
                }
                return false;
            } elseif (!isset($sections[$section])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @dataProvider providePHPTests
     */
    public function testCases(string $name, string $code, array $sections): void {
        if (!isset($_SERVER['_'])) {
            $PHP = 'php';
        } elseif ($_SERVER['_'][0] === '/') {
            $PHP = $_SERVER['_'];
        } else {
            $PHP = realpath($_SERVER['PWD'] . '/' . $_SERVER['_']);
        }
        $descriptorSepc = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        $proc = proc_open("$PHP {$this->BIN}", $descriptorSepc, $pipes);
        fwrite($pipes[0], $code);
        fclose($pipes[0]);
        $result = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($proc);
        $this->assertExpect($result, $sections);
    }

    const ASSERTIONS = [
        'EXPECT' => 'assertEquals',
        'EXPECTF' => 'assertStringMatchesFormat',
        'EXPECTREGEX' => 'assertRegExp',
    ];

    protected function assertExpect(string $result, array $sections): void {
        $actual = preg_replace('(\r\n)', "\n", trim($result));
        foreach (self::ASSERTIONS as $action => $selectedAssertion) {
            if (isset($sections[$action])) {
                $content = preg_replace('(\r\n)', "\n", trim($sections[$action]));
                $expected = $action === "EXPECTREGEX" ? "/{$content}/" : $content;
                if ($expected === null) {
                    throw new \LogicException("No PHPT expectation found");
                }
                $this->$selectedAssertion($expected, $actual);
                return;
            }
        }
        throw new \RuntimeException('No PHPT assertion found');
    }

}