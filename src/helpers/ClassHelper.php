<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\helpers;

use Composer\Autoload\ClassLoader;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Json\JsonValidationException;
use Craft;
use Throwable;
use yii\base\Exception;

/**
 * Class ClassHelper
 *
 * @package GlueAgency\schedule\helpers
 * @author Glue Agency <info@glue.be>
 */
class ClassHelper
{
    /**
     * Returns known component classes.
     *
     * @return string[]
     * @throws JsonValidationException
     * @throws Exception
     */
    public static function findClasses(): array
    {
        // See if Composer has an optimized autoloader
        // h/t https://stackoverflow.com/a/46435124/1688568
        $autoloadClass = null;
        foreach (get_declared_classes() as $class) {
            if (str_starts_with($class, 'ComposerAutoloaderInit')) {
                $autoloadClass = $class;
                break;
            }
        }

        if ($autoloadClass !== null) {
            // Get a list of namespaces we care about
            $namespaces = ['craft'];
            foreach (Craft::$app->getPlugins()->getAllPlugins() as $plugin) {
                $classParts = explode('\\', get_class($plugin));
                if (count($classParts) > 1) {
                    $namespaces[] = implode('\\', array_slice($classParts, 0, -1));
                }
            }

            $jsonPath = Craft::$app->getComposer()->getJsonPath();
            $composer = (new Factory())->createComposer(new NullIO(), $jsonPath);
            $autoload = $composer->getPackage()->getAutoload();
            if (!empty($autoload['psr-4'])) {
                foreach (array_keys($autoload['psr-4']) as $namespace) {
                    $namespaces[] = rtrim($namespace, '\\');
                }
            }

            $namespaces = array_unique($namespaces);

            /** @var ClassLoader $classLoader */
            /** @noinspection PhpUndefinedMethodInspection */
            $classLoader = $autoloadClass::getLoader();
            try {
                foreach ($classLoader->getClassMap() as $class => $file) {
                    if (!class_exists($class, false) &&
                        !interface_exists($class, false) &&
                        !trait_exists($class, false) &&
                        file_exists($file) &&
                        !str_ends_with($class, 'Test') &&
                        !str_ends_with($class, 'TestCase') &&
                        !str_starts_with($class, 'craft\test\\')) {
                        // See if it's in a namespace we care about
                        foreach ($namespaces as $namespace) {
                            if (str_starts_with($class, $namespace . '\\')) {
                                require $file;
                                break;
                            }
                        }
                    }
                }
            } catch (Throwable $e) {
            }

        }

        $classes = get_declared_classes();
        sort($classes);

        return $classes;
    }

    /**
     * @param string $doc
     * @return string|null
     */
    public static function getPhpDocSummary(string $doc)
    {
        foreach (preg_split("/\r\n|\n|\r/", $doc) as $line) {
            $line = preg_replace('#^[/*\s]*(?:@event\s+[^\s]+\s+)?#', '', $line);
            if (str_starts_with($line, '@')) {
                return null;
            }
            if ($line) {
                return $line;
            }
        }

        return null;
    }
}
