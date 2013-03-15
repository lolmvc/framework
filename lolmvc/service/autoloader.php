<?php
namespace Lolmvc\Service;

/**
 * Autoloader implements the PHP Framework Interoperability Group's
 * PHP Standards Recommendation for autoloading classes.
 *
 * http://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 *     // Example which loads classes for the Exegesis annotation parser.
 *     $classLoader = new Autoloader('MattRWallace', ['vendor/matt/src','/vendor/matt']);
 *     $classLoader->register();
 *
 * @author Mitzip <mitzip@lolmvc.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 * @author Lissachenko Alexander <lisachenko.it@gmail.com>
 */
class Autoloader
{
    private $fileExtension = '.php';
    private $namespace;
    private $includePaths;
    private $namespaceSeparator = '\\';

    /**
     * Creates a new <tt>Autoloader</tt> that loads classes of the
     * specified namespace.
     *
     * @param string              $ns The namespace to use.
     * @param string|null|array   $includePath One or more include paths to use
     */
    public function __construct($ns = null, $includePath = null)
    {
        $this->namespace = $ns;
        $this->includePaths = (array) $includePath ?: [];
    }

    /**
     * Sets the namespace separator used by classes in the namespace of this class loader.
     *
     * @param string $sep The separator to use.
     * @return $this
     */
    public function setNamespaceSeparator($sep)
    {
        $this->namespaceSeparator = $sep;
        return $this;
    }

    /**
     * Gets the namespace seperator used by classes in the namespace of this class loader.
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     *
     * @param string|array $includePath One or more include paths
     * @return $this
     */
    public function setIncludePath($includePath)
    {
        $this->includePaths = (array) $includePath;
        return $this;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string|array
     */
    public function getIncludePath()
    {
        return count($this->includePaths) > 1 ? $this->includePaths : reset($this->includePaths);
    }

    /**
     * Sets the file extension of class files in the namespace of this class loader.
     *
     * @param string $fileExtension
     * @return $this
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
        return $this;
    }

    /**
     * Gets the file extension of class files in the namespace of this class loader.
     *
     * @return string $fileExtension
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @return bool
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return bool
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     * @return bool Success status
     */
    public function loadClass($className)
    {
        $isFound = false;
        $nsPrefix = $this->namespace;//.$this->namespaceSeparator;

        if ($this->namespace === null || $nsPrefix === substr($className, 0, strlen($nsPrefix))) {
            $fileName = $this->resolveFileName($className);

            $includePaths = $this->includePaths ?: array('.');
            foreach ($includePaths as $includePath) {
                $unresolvedFilePath = $includePath . /*DIRECTORY_SEPARATOR .*/ $fileName;
                echo "unresolved = " .$unresolvedFilePath."<br />";
                $isFound = $this->tryLoadClassByPath($className, $unresolvedFilePath);
                if ($isFound) {
                    break;
                }
            }
        } else {
            echo "<pre>";
            echo "nsPrefix: " . $nsPrefix . "\n";
            echo "substr = " . substr($className, 0, strlen($nsPrefix)) . "\n";
            echo "</pre>";
        }

        return $isFound;
    }

    /**
     * Tries to load class by path
     *
     * @param string $className Name of the class to load
     * @param string $unresolvedFilePath Absolute or relative path to the file
     * @return bool Success status
     */
    private function tryLoadClassByPath($className, $unresolvedFilePath)
    {
        $verbatim = stream_resolve_include_path($unresolvedFilePath);
        $lowercase = stream_resolve_include_path(strtolower($unresolvedFilePath));
        $filePath = $verbatim ?: $lowercase;
        $isFound  = ($filePath !== false);
        if ($isFound) {
            include $filePath;
            // PHP checks class_exists() after each autoloader, no need to do it here.
            // http://www.php.net/manual/en/function.spl-autoload-register.php#96952
        }
        return $isFound;
    }

    /**
     * Resolves file name to be appended to include path
     *
     * @param string $className Name of the class to load
     * @return string resolved file name
     */
    private function resolveFileName($className)
    {
        $fileName = '';
        $namespace = '';
        if (($lastNsPos = strripos($className, $this->namespaceSeparator)) !== false) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  = strtr($namespace,[$this->namespaceSeparator => DIRECTORY_SEPARATOR]) . DIRECTORY_SEPARATOR;
        }
        $fileName .= strtr($className, ['_' => DIRECTORY_SEPARATOR]) . $this->fileExtension;

        return $fileName;
    }
}


