<?php

namespace Neutrino\Foundation\Cli\Tasks;

use Neutrino\Cli\Task;

/**
 * Class ClearCompiledTask
 *
 * @package Neutrino\Foundation\Cli
 */
class ClearCompiledTask extends Task
{
    /**
     * @description Clear compilation.
     */
    public function mainAction()
    {
        self::clearLoader(BASE_PATH . DIRECTORY_SEPARATOR . 'bootstrap/compile/');

        $this->info('The compiled loader has been removed.');
    }

    /**
     * Handle the post-[install|update] Composer event.
     *
     * @return void
     */
    public static function composerClearCompiled()
    {
        self::clearLoader(getcwd() . DIRECTORY_SEPARATOR . 'bootstrap/compile/');
    }

    /**
     * Deleting Compiled Files
     *
     * @param string $compileDir
     */
    private static function clearLoader($compileDir)
    {
        if (file_exists($compileDir . 'loader.php')) {
            @unlink($compileDir . 'loader.php');
        }
        if (file_exists($compileDir . 'compile.php')) {
            @unlink($compileDir . 'compile.php');
        }
    }
}
