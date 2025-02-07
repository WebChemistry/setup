<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Variant;

/**
 * @template TValue of array<string, mixed>|Variant
 * @implements Variant<TValue>
 */
final readonly class ThemeVariant implements Variant
{

	/** @var callable(callable(mixed $light, mixed $dark): mixed $lightDark): TValue */
	private mixed $factory;

	/**
	 * @param callable(callable(mixed $light, mixed $dark): mixed $lightDark): TValue $factory
	 */
	public function __construct(
		callable $factory,
	)
	{
		$this->factory = $factory;
	}

	/**
	 * @return TValue
	 */
	public function light(): mixed
	{
		return ($this->factory)(fn (mixed $light) => $light);
	}

	/**
	 * @return TValue
	 */
	public function dark(): mixed
	{
		return ($this->factory)(fn (mixed $_, mixed $dark) => $dark);
	}

}
