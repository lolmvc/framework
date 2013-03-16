<?php
namespace Lolmvc\Service;

/**
 * Autoloader implements the PHP Framework Interoperability Group's
 * PHP Standards Recommendation for autoloading classes.
 *
 * http://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 *     // Example which loads classes for the Exegesis annotation parser.
 *     <code>
 *     $classLoader = new Autoloader();
 *     $classLoader->register();
 *     </code>
 *
 * @author Mitzip <mitzip@lolmvc.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 * @author Lissachenko Alexander <lisachenko.it@gmail.com>
 * @package Lolmvc\Service
 * @todo add importComposerNamespaces() method
 */
class Autoloader
{
    private $autoloadRoot;
    private $namespaces;

    /**
     * Creates a new <tt>Autoloader</tt> that loads classes of the
     * specified namespace.
     *
     * @param array $ns The namespaces to load.
     * Example:
     * [
     *  ['Lolmvc' => 'lolmvc'],
     *  ['Skel' => 'skel'],
     *  ['MattRWallace' => 'vendor/mattrwallace']
     * ]
     * @todo remove trailing DIRECTORY_SEPARATORs from namespace paths
     */
    public function __construct($root = null, $namespaces = null)
    {
        $this->autoloadRoot = $root ?: __DIR__;
        $this->namespaces = $namespaces ?: [];
    }

    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     *
     * @param string|array $includePath One or more include paths
     * @return Autoloader this
     */
    public function addNamespace($namespace,$includePath)
    {
        $this->namespaces[] = [$namespace => $includePath];
        return $this;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @return bool
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'normalizedLoad')) &&
            spl_autoload_register(array($this, 'specialLoad'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return bool
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'normalizedLoad')) &&
            spl_autoload_unregister(array($this, 'specialLoad'));
    }


    /**
     *  Emulates default PHP autoloader.
     *  Assumes namespace is directory structure to class file from framework
     *  root, with a lowercase normailized file and path names.
     *
     *  @param string $className The name of the class to load.
     *  @return bool Success status.
     */
    public function normalizedLoad($className)
    {
        return @include $this->autoloadRoot . DIRECTORY_SEPARATOR .
            strtolower($this->resolveFileName($className));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     * @return bool Success status
     */
    public function specialLoad($className)
    {
        // determine if class namespace is known to autoloader by filtering
        // out all namespaces which do not match ^$classname
        $nsPaths = array_filter($this->namespaces, function ($ns) use ($className) {
                        return (strpos($className, key($ns)) === 0) ? 1 : 0;
                    }) ?: [['root' => '']];

        $fileName = $this->resolveFileName($className);
        $lowerFileName = $this->resolveFileName($className, true);

        // walk over all namespace matches from $nsPaths and attempt to load a
        // lowercased filename and a verbatim filename
        // TODO: check if stream_resolve_include_path() will resolve multiple
        // dir separators in-case $nsPaths is just root
        return array_walk($nsPaths, function ($path) use ($fileName,$lowerFileName) {
                $lower = stream_resolve_include_path($this->autoloadRoot .
                    DIRECTORY_SEPARATOR . reset($path) . DIRECTORY_SEPARATOR . $lowerFileName);

                $verbatim = stream_resolve_include_path($this->autoloadRoot .
                    DIRECTORY_SEPARATOR . reset($path) . DIRECTORY_SEPARATOR . $fileName);

                @include $lower ?: $verbatim;
            });
    }

    /**
     * Resolves file name to be appended to include path
     *
     * @param string $className Name of the class to load
     * @return string resolved file name
     */
    private function resolveFileName($className, $lower = null)
    {
        $fileName = '';
        $namespace = '';
        if (($lastNsPos = strripos($className, '\\')) !== false) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = ($lower === true) ? strtolower(substr($className, $lastNsPos + 1)) : substr($className, $lastNsPos + 1);
            $fileName  = strtr($namespace,['\\' => DIRECTORY_SEPARATOR]) . DIRECTORY_SEPARATOR;
        }
        $fileName .= strtr($className, ['_' => DIRECTORY_SEPARATOR]) . '.php';

        return $fileName;
    }
}
