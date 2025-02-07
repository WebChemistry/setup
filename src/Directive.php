<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

/**
 * @template T
 */
abstract class Directive
{

	/**
	 * @param T $value
	 */
	public function __construct(
		protected readonly mixed $value,
	)
	{
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
	 * @return mixed[]
	 */
	public function before(): array
	{
		return [];
	}

	/**
	 * @return mixed[]
	 */
	public function getValues(string $key): array
	{
		if ($this->value instanceof self) {
			return $this->value->getValues($key);
		}

		return [$key => $this->value];
	}

	/**
	 * @return mixed[]
	 */
	public function after(): array
	{
		return [];
	}

	public function isCorrect(SetupContext $context): bool
	{
		return true;
	}

	protected function modifier(string $key, string $modifier): string
	{
		return $key . '#' . $modifier;
	}

}
