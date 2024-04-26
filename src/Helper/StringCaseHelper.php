<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Helper;

use Nette\Utils\Strings;

final class StringCaseHelper
{

	public static function camelToDash(string $input): string
	{
		return strtolower(Strings::replace($input, '/([a-zA-Z])(?=[A-Z])/', '$1-'));
	}

}
