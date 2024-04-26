<?php declare(strict_types = 1);

namespace WebChemistry\Setup\UI;

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

	public function __toString(): string
	{
		return $this->toHex();
	}

}
