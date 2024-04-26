<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

abstract class Directive
{

	protected readonly DirectiveMetadata $metadata;

	public function __construct(
		private mixed $value,
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
	 * @return Directive[]
	 */
	public function getDirectives(): array
	{
		if ($this->value instanceof self) {
			return [$this, ...$this->value->getDirectives()];
		}

		return [$this];
	}

	public function getValue(): mixed
	{
		if ($this->value instanceof self) {
			return $this->value->getValue();
		}

		return $this->value;
	}

}
