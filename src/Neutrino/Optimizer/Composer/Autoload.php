<?php

namespace Neutrino\Optimizer\Composer;

/**
 * Class Autoload
 *
 * @package     Neutrino\Optimizer\Composer
 */
class Autoload
{
    /**
     * @var string
     */
    private $composerPath;

    /**
     * Autoload constructor.
     *
     * @param string $composerPath
     */
    public function __construct($composerPath)
    {
        $this->composerPath = $composerPath;
    }

    /**
     * Return the content of an autoload file.
     *
     * @param string $file
     *
     * @return array
     */
    protected function getAutoloadContent($file)
    {
        $path = $this->composerPath . '/autoload_' . $file . '.php';

        if (file_exists($path)) {
            return require $path;
        }

        return [];
    }

    /**
     * Return registered files added to autoload.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->getAutoloadContent('files');
    }

    /**
     * Return registered namespaces (psr0) added to autoload.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->getAutoloadContent('namespaces');
    }

    /**
     * Return registered namespaces (psr4) added to autoload.
     *
     * @return array
     */
    public function getPsr4()
    {
        return $this->getAutoloadContent('psr4');
    }

    /**
     * Return registered class added to autoload.
     *
     * @return array
     */
    public function getClassmap()
    {
        return $this->getAutoloadContent('classmap');
    }
}
