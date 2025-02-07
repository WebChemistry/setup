<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Helper;

use WebChemistry\Setup\UI\Color;

final class SetupHelper
{

	/**
	 * @param array<string, int|string|Color> $colors
	 * @return array<string, int|string|Color>
	 */
	public function expandToHslColors(array $colors): array
	{
		$values = [];

		foreach ($colors as $name => $color) {
			$values[$name] = $color;

			if (is_string($color)) {
				$color = Color::from($color);
			} else if (!$color instanceof Color) {
				continue;
			}

			[$h, $s, $l] = $color->toHslValues();

			$values[$name . '#h'] = $h;
			$values[$name . '#s'] = sprintf('%d%%', $s);
			$values[$name . '#l'] = sprintf('%d%%', $l);
		}

		return $values;
	}

	/**
	 * @deprecated
	 * @return array<string, int|string>
	 */
	public function expandHsl(string $name, Color|string $color, string $hue = 'H', string $saturation = 'S', string $lightness = 'L'): array
	{
		$color = $color instanceof Color ? $color : Color::from($color);
		[$h, $s, $l] = $color->toHslValues();

		return [
			$name . $hue => $h,
			$name . $saturation => sprintf('%d%%', $s),
			$name . $lightness => sprintf('%d%%', $l),
		];
	}

}
