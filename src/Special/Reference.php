<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Special;

use WebChemistry\Setup\UI\Color;

final class Reference
{

	/** @var list<callable(string|int|Color $value): string> */
	private array $processors = [];

	public function __construct(
		public readonly string $name,
	)
	{
	}

	public function lighten(int $amount): self
	{
		$this->processors[] = static function (mixed $value) use ($amount): string {
			if (is_string($value)) {
				$value = Color::from($value);
			} else if (!$value instanceof Color) {
				throw new \LogicException(sprintf('Value %s is not a color.', self::debugType($value)));
			}

			return (string) $value->lighten($amount);
		};

		return $this;
	}

	public function darken(int $amount): self
	{
		$this->processors[] = static function (string|int|Color $value) use ($amount): string {
			if (is_string($value)) {
				$value = Color::from($value);
			} else if (!$value instanceof Color) {
				throw new \LogicException(sprintf('Value %s is not a color.', self::debugType($value)));
			}

			return (string) $value->darken($amount);
		};

		return $this;
	}

	public function process(string|int|Color $value): mixed
	{
		foreach ($this->processors as $processor) {
			$value = $processor($value);
		}

		return $value;
	}

	private static function debugType(mixed $value): string
	{
		if (is_scalar($value)) {
			return (string) $value;
		}

		if ($value === null) {
			return '(null)';
		}

		return sprintf('(%s)', get_debug_type($value));
	}

}
