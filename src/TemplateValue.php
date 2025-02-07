<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class TemplateValue
{

	/** @var callable(ContentBuilder $builder): void */
	private mixed $fn;

	/**
	 * @param callable(ContentBuilder $builder): void $fn
	 */
	public function __construct(
		callable $fn,
	)
	{
		$this->fn = $fn;
	}

	/**
	 * @param ContentBuilder $builder
	 */
	public function call(ContentBuilder $builder): void
	{
		($this->fn)($builder);
	}

}
