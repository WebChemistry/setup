<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

/**
 * @template T of mixed
 */
abstract class Directive
{

	protected readonly DirectiveMetadata $metadata;

	/**
	 * @param T $value
	 */
	public function __construct(
		protected readonly mixed $value,
	)
	{
		$this->metadata = new DirectiveMetadata();
	}

	public function getMetadata(): DirectiveMetadata
	{
		if ($this->value instanceof self) {
			return $this->metadata->merge($this->value->getMetadata());
		}

		return $this->metadata;
	}

	/**
	 * @return Directive<mixed>[]
	 */
	public function getDirectives(): array
	{
		if ($this->value instanceof self) {
			return [$this, ...$this->value->getDirectives()];
		}

		return [$this];
	}

	/**
	 * @return T|FlattenValue<T>
	 */
	public function getValue(string $key): mixed
	{
		if ($this->value instanceof self) {
			return $this->value->getValue($key);
		}

		return $this->value;
	}

}
