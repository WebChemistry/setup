<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Helper;

use WebChemistry\Setup\UI\Color;

final class SetupHelper
{

	/**
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
