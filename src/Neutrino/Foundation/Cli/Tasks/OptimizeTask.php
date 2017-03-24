<?php

namespace Neutrino\Foundation\Cli\Tasks;

use ClassPreloader\Factory;
use Neutrino\Cli\Task;
use Neutrino\Dotenv;
use Neutrino\Optimizer\Composer;

/**
 * Class OptimizeTask
 *
 * @package Neutrino\Foundation\Cli
 */
class OptimizeTask extends Task
{
    /**
     * @var Composer
     */
    private $optimizer;

    /**
     * Optimize the autoloader.
     *
     * @description Optimize the autoloader.
     *
     * @option      -m, --memory: Optimize memory.
     */
    public function mainAction()
    {
        $this->optimizer = new Composer(
            Dotenv::env('BASE_PATH') . '/bootstrap/compile/loader.php',
            Dotenv::env('BASE_PATH') . '/vendor/composer',
            Dotenv::env('BASE_PATH')
        );

        if ($this->hasOption('m', 'memory')) {
            $res = $this->optimizeMemory();
        } else {
            $res = $this->optimizeProcess();
        }
        if ($res === false) {
            $this->error('Autoloader generation has failed');
        }

        $this->optimizeClass();

        $this->info('Compiling common classes');

        $this->dispatcher->forward([
            'task' => ConfigCacheTask::class
        ]);
    }

    /**
     * Build an memory optimized autoloader
     *
     * @return bool|int
     */
    protected function optimizeMemory()
    {
        $this->info('Generating memory optimized auto-loader');

        return $this->optimizer->optimizeMemory();
    }

    /**
     * Build an process optimized autoloader
     *
     * @return bool|int
     */
    protected function optimizeProcess()
    {
        $this->info('Generating optimized auto-loader');

        return $this->optimizer->optimizeProcess();
    }

    protected function optimizeClass()
    {
        $preloader = (new Factory())->create(['skip' => true]);

        $handle = $preloader->prepareOutput(Dotenv::env('BASE_PATH') . '/bootstrap/compile/compile.php');

        $files = require __DIR__ . '/Optimize/compile.php';

        if (file_exists(Dotenv::env('BASE_PATH') . '/config/compile.php')) {
            $files = array_merge($files, require Dotenv::env('BASE_PATH') . '/config/compile.php');
        }

        foreach ($files as $file) {
            try {
                fwrite($handle, $preloader->getCode($file, false) . PHP_EOL);
            } catch (\Exception $e) {
                //
            }
        }

        fclose($handle);
    }
}
