<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use JetBrains\PhpStorm\ExpectedValues;
use WebChemistry\Setup\Directives\ColorAlterDirective;
use WebChemistry\Setup\Directives\CommentDirective;
use WebChemistry\Setup\Directives\ConditionDirective;
use WebChemistry\Setup\Directives\DeprecatedDirective;
use WebChemistry\Setup\Directives\HslDirective;
use WebChemistry\Setup\Directives\SectionDirective;
use WebChemistry\Setup\UI\Color;

final class SetupDirectives
{

	public function deprecated(mixed $value, string $comment = ''): DeprecatedDirective
	{
		return new DeprecatedDirective($value, $comment);
	}

	public function comment(mixed $value, string $comment): CommentDirective
	{
		return new CommentDirective($value, $comment);
	}

	/**
	 * @param mixed[] $values
	 */
	public function section(array $values, string $name): SectionDirective
	{
		return new SectionDirective($values, $name);
	}

	/**
	 * @param callable(SetupContext $context): bool $condition
	 */
	public function condition(mixed $value, callable $condition): ConditionDirective
	{
		return new ConditionDirective($value, $condition);
	}

	/**
	 * @param string|Color $value
	 */
	public function hsl(string|Color $value): HslDirective
	{
		return new HslDirective($value);
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
