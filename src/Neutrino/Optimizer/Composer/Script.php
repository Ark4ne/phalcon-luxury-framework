<?php

namespace Neutrino\Optimizer\Composer;

/**
 * Class Script
 *
 * @package     Neutrino\Optimizer\Composer
 */
class Script
{
    /**
     * @var string
     */
    private $basePath;

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath;
    }

    /**
     * Call the dump-autoload composer command.
     *
     * @param bool $optimize
     */
    public function dumpautoload($optimize = false)
    {
        $this->callComposer('dump-autoload', [
            'optimize' => $optimize
        ]);
    }

    /**
     * Return the base path, where composer will may get called
     *
     * @return string
     */
    public function getBasePath()
    {
        if (is_null($this->basePath)) {
            $this->setBasePath(getcwd());
        }

        return $this->basePath;
    }

    /**
     * Set the base path, where composer will may get called
     *
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Return the composer command line
     *
     * @return string
     */
    protected function getComposerCmd()
    {
        if (!file_exists($this->getBasePath() . '/composer.phar')) {
            return 'composer';
        }

        $binary = ($phpBinary = getenv('PHP_BINARY')) ? $phpBinary : PHP_BINARY;

        return $binary . ' composer.phar';
    }

    protected function buildComposerCmd($action, $options = [])
    {
        $cmd = trim($this->getComposerCmd() . ' ' . $action);

        foreach ($options as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $cmd .= ' --' . $key;
                }
            } else {
                $cmd .= ' --' . $key . '=' . $value;
            }
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            $cmd = 'cmd /Q /C "' . $cmd . '" > NUL 2> NUL';
        } else {
            $cmd = $cmd . ' 1> /dev/null 2> /dev/null';
        }

        return $cmd;
    }

    /**
     * Call composer
     *
     * @param string $action
     * @param array  $options
     */
    protected function callComposer($action, $options = [])
    {
        $cmd = $this->buildComposerCmd($action, $options);

        $resource = proc_open($cmd, [], $pipes, $this->getBasePath());

        foreach ($pipes as $pipe) {
            fclose($pipe);
        }

        proc_close($resource);
    }
}
