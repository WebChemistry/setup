<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use JetBrains\PhpStorm\ExpectedValues;
use WebChemistry\Setup\Directives\ColorAlterDirective;
use WebChemistry\Setup\Directives\DeprecatedDirective;

final class SetupDirectives
{

	public function deprecated(mixed $value, string $name = ''): DeprecatedDirective
	{
		return new DeprecatedDirective($value, $name);
	}

	/**
	 * @param list<string> $array
	 */
	public function colorAlter(
		string $string,
		#[ExpectedValues(['hsl', 'hslArgs', 'rgb', 'rgbArgs', 'hex', 'hexArgs'])]
		array $array,
	): ColorAlterDirective
	{
		return new ColorAlterDirective($string, $array);
	}

}
