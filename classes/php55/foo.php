<?php

namespace autophp\php55;

class foo extends \autophp\php54\foo
{
	function displayPhpVersion()
	{
		echo __NAMESPACE__ . PHP_EOL;
	}
}
