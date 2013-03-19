<?php
namespace Lolmvc\Service;

/**
 * Autoloader implements the PHP Framework Interoperability Group's
 * PHP Standards Recommendation for autoloading classes.
 *
 * http://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 *      // Example which loads classes for the Exegesis annotation parser.
 *      $loader = new Autoloader();
 *      $loader
 *          ->addNamespaces([['MattRWallace\\Exegesis' => 'vendor']])
 *          ->importComposerNamespaces()
 *          ->register();
 *
 * @author Mitzip <mitzip@lolmvc.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 * @author Lissachenko Alexander <lisachenko.it@gmail.com>
 * @package Lolmvc\Service
 * @todo Implement the ability to change the namespace seperator
 * @todo Make usage of addNamespaces more intuitive
 * @todo completely docu/comment and fix authors
 */
class Autoloader
{
    private $autoloadRoot;
    private $namespaces;

    /**
     * Creates a new <tt>Autoloader</tt> that loads classes of the
     * specified namespace.
     *
     * Features:
     * Multiple include paths per namespace.
     * Multiple namespaces per include path.
     * Both absolute and relative to root paths.
     *
     *      // Example array of namespaces to pass to Autoloader
     *      $namespaces = [
     *          ['Cool\\CoolLib' => 'library/coollib/src'],
     *          ['Neat\\NeatLib' => 'library/neatlib'],
     *          ['Awe\\AwesomeLib' => 'library/coollib/src'],
     *          ['Cool\\CoolLib' => 'library/coollib'],
     *          ['MattRWallace\\Exegesis' => 'vendor']
     *      ];
     *
     * @param string root directory that all include paths are resolved against
     * @param array $namespaces The namespaces to load.
    */
    public function __construct($root = null, $namespaces = [])
    {
        $this->autoloadRoot = $root ?: '..' . DIRECTORY_SEPARATOR . '..';
        $this->namespaces = $this->trimPaths($namespaces);
    }

    /**
     * Trims away trailing DIRECTORY_SEPARATORs from paths
     *
     * @param array namespace array
     * @return array trimmed namespace array or null array
     */
    private function trimPaths($pathsToTrim = [])
    {
        $trimmedPaths = [];
        if ($pathsToTrim) {
            $trimmedPaths = array_map(function ($includePath) {
                        $trimmed = rtrim(reset($includePath), DIRECTORY_SEPARATOR);
                        return [key($includePath) => $trimmed];
                    },$pathsToTrim);
        }
        return $trimmedPaths;
    }

    /**
     * Imports the Composer namespace/include paths
     *
     * @param string Composer dependency install path root
     * @return Autoloader this
     */
    public function importComposerNamespaces($composerRoot = 'vendor')
    {
        $composerNamespaces = include $this->autoloadRoot . DIRECTORY_SEPARATOR .
            $composerRoot . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR .
            'autoload_namespaces.php';

        if ($composerNamespaces) { // if composer has namespaces for deps
            foreach ($composerNamespaces as $ns => $includePath) {
                if (strlen($ns) !== 0) { // if namespace specified, unspecified composer catchall not supported
                    if (is_array($includePath)) { // if namespace has multiple include paths
                        foreach ($includePath as $multipath) {
                            $this->namespaces[] = [$ns => $multipath];
                        }
                    } else { // if namespace has only a single include path
                        $this->namespaces[] = [$ns => $includePath];
                    }
                }
            }
        }
        $this->namespaces = $this->trimPaths($this->namespaces);
        return $this;
    }


    /**
     * Add namespaces after the constructor has executed.
     *
     * @param array accepts namespaces defined just like for the contructor
     * @return Autoloader this
     */
    public function addNamespaces($namespaces)
    {
        $trimmedNamespaces = $this->trimPaths($namespaces);
        foreach ($trimmedNamespaces as $ns) {
            $this->namespaces[] = $ns;
        }
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
        return spl_autoload_register(array($this, 'findFile'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return bool
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'findFile'));
    }


    /**
     * Loads file that is supposed to contain class definition
     *
     * @param string $className The name of the class to load.
     * @return bool Success status
     */
    public function findFile($className)
    {
        $foundFile = false;
        $pathFromNamespace = $this->resolveRelativeFilePath($className);
        $fileName = '';

        // determine file name from resolved namespace, or class name if necessary
        if (($lastNsPos = strrpos($pathFromNamespace, DIRECTORY_SEPARATOR)) !== false) {
            $fileName = substr($pathFromNamespace, $lastNsPos + 1);
        } else {
            $fileName = $className . '.php';
        }

        // determine if class namespace is known to autoloader by filtering
        // out all namespaces which do not match ^$classname
        if ($this->namespaces) {
            $nsIncludePathsAvailable = array_filter($this->namespaces, function ($ns) use ($className) {
                    return (strpos($className, key($ns)) === 0) ? 1 : 0;
                });
        } else {
            $nsIncludePathsAvailable = null;
        }

        if ($nsIncludePathsAvailable) {
            array_map(function ($includePath) use ($foundFile,$pathFromNamespace,$fileName) {
                $foundFile =
                    // absolute path directly to namespaced files, normalized
                    stream_resolve_include_path(reset($includePath) . DIRECTORY_SEPARATOR . strtolower($fileName))
                    ?:
                    // absolute path directly to namespaced files, verbatim
                    stream_resolve_include_path(reset($includePath) . DIRECTORY_SEPARATOR . $fileName)
                    ?:
                    // autoload root relative include path, normalized
                    stream_resolve_include_path($this->autoloadRoot .
                    DIRECTORY_SEPARATOR . reset($includePath) . DIRECTORY_SEPARATOR . strtolower($pathFromNamespace))
                    ?:
                    // autoload root relative include path, verbatim
                    stream_resolve_include_path($this->autoloadRoot .
                    DIRECTORY_SEPARATOR . reset($includePath) . DIRECTORY_SEPARATOR . $pathFromNamespace)
                    ?:
                    // absolute include path, with namespace resolved path appended, verbatim
                    stream_resolve_include_path(reset($includePath) . DIRECTORY_SEPARATOR . $pathFromNamespace)
                    ?:
                    // absolute include path, with namespace resolved path appended, normalized
                    stream_resolve_include_path(reset($includePath) . DIRECTORY_SEPARATOR . strtolower($pathFromNamespace));
                if ($foundFile) return include $foundFile;
            },$nsIncludePathsAvailable);
            return $foundFile;
        } else { // if no paths are available, try root
            $foundFile =
                // normalized from root
                stream_resolve_include_path($this->autoloadRoot . DIRECTORY_SEPARATOR .
                strtolower($pathFromNamespace))
                ?:
                // verbatim from root
                stream_resolve_include_path($this->autoloadRoot . DIRECTORY_SEPARATOR .
                $pathFromNamespace);
            return ($foundFile) ? include $foundFile : $foundFile;
        }
    }

    /**
     * Resolves file path from namespace, to be appended to include path
     *
     * @param string $className Name of the class to load
     * @return string resolved file name
     */
    private function resolveRelativeFilePath($className)
    {
        $relativeFilePath = '';
        $namespace = '';
        if (($lastNsPos = strrpos($className, '\\')) !== false) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $relativeFilePath = strtr($namespace,['\\' => DIRECTORY_SEPARATOR]) . DIRECTORY_SEPARATOR;
        }
        $relativeFilePath .= strtr($className, ['_' => DIRECTORY_SEPARATOR]) . '.php';

        return $relativeFilePath;
    }
}
