<?php

namespace autophp;

class autoloader
{
	const defaultFileSuffix = '.php';

	private $directories = array();
	private $namespaceAliases = array();

	private static $autoloader = null;

	function register($prepend = false)
	{
		if (spl_autoload_register(array($this, 'requireClass'), true, $prepend) === false)
		{
			throw new \runtimeException('Unable to register autoloader \'' . get_class($this) . '\'');
		}

		return $this;
	}

	function requireClass($class)
	{
		$class = strtolower($class);

		$realClasses = $this->resolveNamespaceAliases($class);

		foreach ($realClasses as $realClass)
		{
			if (static::exists($realClass) === false && ($path = $this->getPath($realClass)) !== null)
			{
				require $path;
			}

			if (static::exists($realClass) === true)
			{
				if ($realClass !== $class)
				{
					class_alias($realClass, $class);
				}

				break;
			}
		}
	}

	function addDirectory($namespace, $directory, $suffix = self::defaultFileSuffix)
	{
		$namespace = strtolower(trim($namespace, '\\') . '\\');
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if (isset($this->directories[$namespace][$directory]) === false)
		{
			$this->directories[$namespace][$directory] = $suffix;

			krsort($this->directories, \SORT_STRING);
		}

		return $this;
	}

	function addNamespaceAlias($alias, $target)
	{
		$this->namespaceAliases[strtolower(trim($alias, '\\')) . '\\'] = ($target === null ? null : array(trim($target, '\\') . '\\'));

		krsort($this->namespaceAliases, \SORT_STRING);

		return $this;
	}

	function notAliasNamespace($namespace)
	{
		return $this->addNamespaceAlias($namespace, null);
	}

	function addNamespaceAliases($alias, array $targets)
	{
		foreach ($targets as & $target)
		{
			$target =  trim($target, '\\') . '\\';
		}

		$this->namespaceAliases[strtolower(trim($alias, '\\')) . '\\'] = $targets;

		krsort($this->namespaceAliases, \SORT_STRING);

		return $this;
	}

	public static function set()
	{
		if (static::$autoloader === null)
		{
			static::$autoloader = new static();
			static::$autoloader->register();
		}

		return static::$autoloader;
	}

	public static function get()
	{
		return static::set();
	}

	private function getPath($class)
	{
		foreach ($this->directories as $namespace => $directory)
		{
			if (strpos($class, $namespace) === 0)
			{
				$namespaceLength = strlen($namespace);

				$path = key($directory) . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $namespaceLength)) . current($directory);

				if (is_file($path))
				{
					return $path;
				}
			}
		}

		return null;
	}

	private function resolveNamespaceAliases($class)
	{
		$classes = array();

		foreach ($this->namespaceAliases as $alias => $targets)
		{
			if (strpos($class, $alias) === 0)
			{
				if ($targets === null)
				{
					$classes = array($class);
				}
				else foreach ($targets as $target)
				{
					$classes[] = $target . substr($class, strlen($alias));
				}

				break;
			}
		}

		return $classes;
	}

	private function getNamespaceAlias($class)
	{
		foreach ($this->namespaceAliases as $alias => $target)
		{
			if (strpos($class, $target) === 0)
			{
				return $alias . substr($class, strlen($target));
			}
		}

		return null;
	}

	private function handleNamespaceOfClass($class)
	{
		foreach (array_keys($this->directories) as $namespace)
		{
			if (strpos($class, $namespace) === 0)
			{
				return true;
			}
		}

		return false;
	}

	private static function exists($class)
	{
		return class_exists($class, false) || interface_exists($class, false);
	}
}

autoloader::set();
