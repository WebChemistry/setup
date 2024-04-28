<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

/**
 * @template T of mixed
 */
final class FlattenValue
{

	/**
	 * @param array<string, T> $values
	 */
	public function __construct(
		public readonly array $values,
	)
	{
	}

}
