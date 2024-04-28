<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Directives;

use JetBrains\PhpStorm\ExpectedValues;
use LogicException;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\FlattenValue;
use WebChemistry\Setup\UI\Color;

/**
 * @extends Directive<string>
 */
final class ColorAlterDirective extends Directive
{

	private const Names = ['hslArgs' => 'hsl', 'rgbArgs' => 'rgb'];

	/**
	 * @param array<string> $formats
	 */
	public function __construct(
		string $value,
		#[ExpectedValues(['hsl', 'hslArgs', 'rgb', 'rgbArgs', 'hex'])]
		private readonly array $formats,
	)
	{
		parent::__construct($value);
	}

	public function getValue(string $key): mixed
	{
		$colors = [
			$key => $this->value,
		];

		foreach ($this->formats as $name => $format) {
			$name = is_int($name) ? self::Names[$format] ?? $format : $name;

			$colors[$key . ucfirst($name)] = $this->toFormat($format);
		}

		return new FlattenValue($colors);
	}

	private function toFormat(string $format): string
	{
		$color = Color::from($this->value);

		if ($format === 'hsl') {
			return $color->toHsl();
		} elseif ($format === 'rgb') {
			return $color->toRgb();
		} elseif ($format === 'hex') {
			return $color->toHex();
		} else if ($format === 'hslArgs') {
			return $color->toHslArgs();
		} elseif ($format === 'rgbArgs') {
			return $color->toRgbArgs();
		} else {
			throw new LogicException('Invalid format.');
		}
	}

}
