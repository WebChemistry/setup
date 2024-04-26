<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use WebChemistry\Setup\Directives\DeprecatedDirective;

final class SetupDirectives
{

	public function deprecated(mixed $value, string $name = ''): DeprecatedDirective
	{
		return new DeprecatedDirective($value, $name);
	}

}
