<?php declare(strict_types = 1);

namespace WebChemistry\Setup\UI;

use InvalidArgumentException;
use Stringable;

class Color implements Stringable
{

	public function __construct(
		private int $red,
		private int $green,
		private int $blue,
	)
	{
	}

	public static function fromHex(string $hex): Color
	{
		$hex = ltrim($hex, '#');

		if (strlen($hex) == 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		[$red, $green, $blue] = array_map(
			fn (string $hex) => (int) hexdec($hex),
			str_split($hex, 2),
		);

		return new self($red, $green, $blue);
	}

	public static function from(string $color): Color
	{
		if (str_starts_with($color, '#')) {
			return self::fromHex($color);
		}

		if (str_starts_with($color, 'rgb')) {
			return self::fromRgb($color);
		}

		if (str_starts_with($color, 'hsl')) {
			return self::fromHsl($color);
		}

		throw new InvalidArgumentException('Invalid color format');
	}

	public static function fromRgb(string $color): Color
	{
		$color = str_replace(['rgb(', ')'], '', $color);

		[$red, $green, $blue] = array_map(
			function (string $color): int {
				$color = trim($color);

				if (!is_numeric($color)) {
					throw new InvalidArgumentException('Invalid color format');
				}

				return (int) $color;
			},
			explode(',', $color));

		return new self($red, $green, $blue);
	}

	public static function fromHsl(string $color): Color
	{
		$color = str_replace(['hsl(', ')'], '', $color);

		[$hue, $saturation, $lightness] = array_map(
			function (string $color): int {
				$color = trim($color);

				if (!is_numeric($color)) {
					throw new InvalidArgumentException('Invalid color format');
				}

				return (int) $color;
			},
			explode(',', $color));

		$lightness /= 100;
		$saturation /= 100;

		if ($saturation === 0) {
			$red = $green = $blue = $lightness * 255;
		} else {
			$chroma = (1 - abs(2 * $lightness - 1)) * $saturation;
			$hue = $hue / 60;
			$intermediate = $chroma * (1 - abs(fmod($hue, 2) - 1));
			$red = $green = $blue = 0;

			if ($hue >= 0 && $hue < 1) {
				$red = $chroma;
				$green = $intermediate;
			} elseif ($hue >= 1 && $hue < 2) {
				$red = $intermediate;
				$green = $chroma;
			} elseif ($hue >= 2 && $hue < 3) {
				$green = $chroma;
				$blue = $intermediate;
			} elseif ($hue >= 3 && $hue < 4) {
				$green = $intermediate;
				$blue = $chroma;
			} elseif ($hue >= 4 && $hue < 5) {
				$red = $intermediate;
				$blue = $chroma;
			} elseif ($hue >= 5 && $hue < 6) {
				$red = $chroma;
				$blue = $intermediate;
			}

			$lightness -= $chroma / 2;
			$red = ($red + $lightness) * 255;
			$green = ($green + $lightness) * 255;
			$blue = ($blue + $lightness) * 255;
		}

		return new self($red, $green, $blue);
	}

	public function getContrastWhiteOrBlack(): Color
	{
		// Calc contrast ratio
		$L1 = 0.2126 * pow($this->red / 255, 2.2) +
			  0.7152 * pow($this->green / 255, 2.2) +
			  0.0722 * pow($this->blue / 255, 2.2);

		$L2 = 0.2126 * pow(0 / 255, 2.2) +
			  0.7152 * pow(0 / 255, 2.2) +
			  0.0722 * pow(0 / 255, 2.2);

		if ($L1 > $L2) {
			$contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
		} else {
			$contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
		}

		if ($contrastRatio > 5) {
			return new Color(0, 0, 0);
		} else {
			return new Color(255, 255, 255);
		}
	}

	public function lighten(int $amount): Color
	{
		return new self(...$this->adjustBrightness($amount / 100));
	}

	public function darken(int $amount): Color
	{
		return new self(...$this->adjustBrightness(-($amount / 100)));
	}

	/**
	 * @param float $adjustPercent A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
	 * @return array{int, int, int}
	 */
	private function adjustBrightness(float $adjustPercent): array
	{
		$adjust = function (float $adjustPercent, int $color): int {
			$adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
			$adjustAmount = ceil($adjustableLimit * $adjustPercent);

			return (int) ($color + $adjustAmount);
		};

		return [
			$adjust($adjustPercent, $this->red),
			$adjust($adjustPercent, $this->green),
			$adjust($adjustPercent, $this->blue),
		];
	}

	public function toHex(): string
	{
		return sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
	}

	public function toRgb(): string
	{
		return sprintf('rgb(%s)', $this->toRgbArgs());
	}

	public function toRgbArgs(): string
	{
		return sprintf('%d, %d, %d', $this->red, $this->green, $this->blue);
	}

	public function toHsl(): string
	{
		return sprintf('hsl(%s)', $this->toHslArgs());
	}

	public function toHslArgs(): string
	{
		[$hue, $saturation, $lightness] = $this->toHslValues();

		return sprintf('%d, %d%%, %d%%', $hue, $saturation, $lightness);
	}

	/**
	 * @return array{int, int, int}
	 */
	public function toRgbValues(): array
	{
		return [$this->red, $this->green, $this->blue];
	}

	/**
	 * @return array{int, int, int}
	 */
	public function toHslValues(): array
	{
		// Normalize RGB values
		$red = $this->red / 255;
		$green = $this->green / 255;
		$blue = $this->blue / 255;

		// Find the maximum and minimum values of RGB
		$max = max($red, $green, $blue);
		$min = min($red, $green, $blue);

		// Calculate the lightness
		$lightness = ($max + $min) / 2;

		// Check for pure gray color
		if ($max === $min) {
			$hue = $saturation = 0; // Hue and saturation are 0 for gray
		} else {
			// Calculate the saturation
			if ($lightness < 0.5) {
				$saturation = ($max - $min) / ($max + $min);
			} else {
				$saturation = ($max - $min) / (2 - $max - $min);
			}

			// Calculate the hue
			if ($max == $red) {
				$hue = ($green - $blue) / ($max - $min);
			} elseif ($max == $green) {
				$hue = 2 + ($blue - $red) / ($max - $min);
			} else {
				$hue = 4 + ($red - $green) / ($max - $min);
			}

			$hue *= 60; // Convert hue to degrees
			if ($hue < 0) {
				$hue += 360; // Ensure hue is within [0,360] range
			}
		}

		return [(int) round($hue), (int) round($saturation * 100), (int) round($lightness * 100)];
	}

	public function __toString(): string
	{
		return $this->toHex();
	}

}
