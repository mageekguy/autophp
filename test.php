<?php

namespace autophp;

require __DIR__ . '/classes/autoloader.php';

$autoloader = autoloader::get();

switch (true)
{
	case PHP_VERSION_ID >= 50500:
		$autoloader
			->addDirectory('autophp\php55', __DIR__ . '/classes/php55')
			->addDirectory('autophp\php54', __DIR__ . '/classes/php54')
			->addDirectory('autophp\php53', __DIR__ . '/classes/php53')
			->notAliasNamespace('autophp\php53')
			->notAliasNamespace('autophp\php54')
			->addNamespaceAliases('autophp', [ 'autophp\php55', 'autophp\php54', 'autophp\php53' ])
		;
		break;

	case PHP_VERSION_ID >= 50400:
		$autoloader
			->addDirectory('autophp\php54', __DIR__ . '/classes/php54')
			->addDirectory('autophp\php53', __DIR__ . '/classes/php53')
			->notAliasNamespace('autophp\php53')
			->addNamespaceAliases('autophp', [ 'autophp\php54', 'autophp\php53' ])
		;
		break;

	case PHP_VERSION_ID >= 50300:
		$autoloader
			->addDirectory('autophp\php53', __DIR__ . '/classes/php53')
			->addNamespaceAlias('autophp', 'autophp\php53')
		;
		break;

	default:
		throw new \runtimeException('Unsupported PHP version');
}

(new foo())->displayPhpVersion();
(new bar())->displayPhpVersion();
