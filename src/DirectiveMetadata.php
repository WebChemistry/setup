<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use InvalidArgumentException;

final class DirectiveMetadata
{

	/** @var array<string, list<scalar>> */
	private array $metadata = [];

	public function add(string $name, string|float|int|bool $value): self
	{
		$this->metadata[$name][] = $value;

		return $this;
	}

	/**
	 * @param string $name
	 * @return mixed[]|null
	 */
	public function get(string $name): ?array
	{
		return $this->metadata[$name] ?? null;
	}

	/**
	 * @return scalar[]
	 */
	public function getScalar(string $name, bool $skipNulls = true): array
	{
		$values = $this->metadata[$name] ?? [];

		foreach ($values as $value) {
			if ($skipNulls && $value === null) { // @phpstan-ignore-line
				continue;
			}

			if (!is_scalar($value)) {
				throw new InvalidArgumentException(sprintf('Value must be scalar, %s given.', gettype($value)));
			}
		}

		return $values;
	}

	public function merge(DirectiveMetadata $metadata): self
	{
		$clone = new self();

		foreach ($this->metadata as $name => $values) {
			foreach ($values as $value) {
				$clone->add($name, $value);
			}
		}

		foreach ($metadata->metadata as $name => $values) {
			foreach ($values as $value) {
				$clone->add($name, $value);
			}
		}

		return $clone;
	}

}
