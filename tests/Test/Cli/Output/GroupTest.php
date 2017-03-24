<?php

namespace Test\Cli\Output;

use Neutrino\Cli\Output\ConsoleOutput;
use Neutrino\Cli\Output\Group;
use Test\Stub\StubConsoleOutput;
use Test\TestCase\TestCase;

/**
 * Class GroupTest
 *
 * @package Test\Cli\Output
 */
class GroupTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // Force Enable Decoration for windows
        putenv('TERM=xterm');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        // Force Enable Decoration for windows
        putenv('TERM=');
    }

    /**
     * @return mixed|ConsoleOutput
     */
    private function consoleOutput()
    {
        return new StubConsoleOutput();
    }

    public function dataGenerateGroupData()
    {
        return [
            [[
                 'default' => ['list' => 'all commands'],
                 'route'   => ['route:list' => 'all routes'],
             ],
             [
                 'list'       => 'all commands',
                 'route:list' => 'all routes',
             ]],
            [[
                 'default' => ['list' => 'all commands'],
                 'route'   => [
                     'route:list'  => 'all routes',
                     'route:cache' => 'cache routes',
                     'route:clear' => 'clear cache routes',
                 ],
                 'view'    => ['view:clear' => 'clear views'],
             ],
             [
                 'list'        => 'all commands',
                 'route:list'  => 'all routes',
                 'route:cache' => 'cache routes',
                 'route:clear' => 'clear cache routes',
                 'view:clear'  => 'clear views',
             ]],
            [[
                 'default' => ['list' => 'all commands'],
                 'route'   => [
                     'route:list'                  => 'all routes',
                     "\033[32mroute:cache\033[39m" => 'cache routes',
                     "\033[32mroute:clear\033[39m" => 'clear cache routes',
                 ],
                 'view'    => ["\033[32mview:clear\033[39m" => 'clear views'],
             ],
             [
                 'list'                        => 'all commands',
                 'route:list'                  => 'all routes',
                 "\033[32mroute:cache\033[39m" => 'cache routes',
                 "\033[32mroute:clear\033[39m" => 'clear cache routes',
                 "\033[32mview:clear\033[39m"  => 'clear views',
             ]],
        ];
    }

    /**
     * @dataProvider dataGenerateGroupData
     */
    public function testGenerateGroupData($expected, $data)
    {
        $output = $this->consoleOutput();

        $table = new Group($output, $data);

        $this->invokeMethod($table, 'generateGroupData', []);

        $columns = $this->getValueProperty($table, 'groups');

        $this->assertEquals($expected, $columns);
    }

    public function dataDisplay()
    {
        return [
            [
                ' list        all commands ' . PHP_EOL .
                "\033[33mroute\033[39m" . PHP_EOL .
                ' route:list  all routes   ' . PHP_EOL,
                [
                    'list'       => 'all commands',
                    'route:list' => 'all routes',
                ]
            ],
            [
                ' list         all commands       ' . PHP_EOL .
                "\033[33mroute\033[39m" . PHP_EOL .
                ' route:list   all routes         ' . PHP_EOL .
                ' route:cache  cache routes       ' . PHP_EOL .
                ' route:clear  clear cache routes ' . PHP_EOL .
                "\033[33mview\033[39m" . PHP_EOL .
                ' view:clear   clear views        ' . PHP_EOL,
                [
                    'list'        => 'all commands',
                    'route:list'  => 'all routes',
                    'route:cache' => 'cache routes',
                    'route:clear' => 'clear cache routes',
                    'view:clear'  => 'clear views',
                ]
            ],
            [

                ' list         all commands       ' . PHP_EOL .
                "\033[33mroute\033[39m" . PHP_EOL .
                ' route:list   all routes         ' . PHP_EOL .
                " \033[32mroute:cache\033[39m  cache routes       " . PHP_EOL .
                " \033[32mroute:clear\033[39m  clear cache routes " . PHP_EOL .
                "\033[33mview\033[39m" . PHP_EOL .
                " \033[32mview:clear\033[39m   clear views        " . PHP_EOL,
                [
                    'list'                        => 'all commands',
                    'route:list'                  => 'all routes',
                    "\033[32mroute:cache\033[39m" => 'cache routes',
                    "\033[32mroute:clear\033[39m" => 'clear cache routes',
                    "\033[32mview:clear\033[39m"  => 'clear views',
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataDisplay
     */
    public function testDisplay($expected, $data)
    {
        $output = $this->consoleOutput();

        $table = new Group($output, $data);

        $this->invokeMethod($table, 'display', []);

        $this->assertEquals($expected, $output->out);
    }
}
