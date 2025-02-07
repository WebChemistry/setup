<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Variant;

final class Variants
{

	/**
	 * @template TValue of array<string, mixed>|Variant
	 * @param callable(callable(mixed $light, mixed $dark): mixed $lightDark): TValue $fn
	 * @return ThemeVariant<TValue>
	 */
	public function theme(callable $fn): ThemeVariant
	{
		return new ThemeVariant($fn);
	}

}
