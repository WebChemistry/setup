<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Directives;

use WebChemistry\Setup\Directive;
use WebChemistry\Setup\SetupContext;

/**
 * @extends Directive<mixed>
 */
final class ConditionDirective extends Directive
{

	/** @var callable(SetupContext $context): bool */
	private mixed $condition;

	/**
	 * @param callable(SetupContext $context): bool $condition
	 */
	public function __construct(mixed $value, callable $condition)
	{
		parent::__construct($value);

		$this->condition = $condition;
	}

	public function isCorrect(SetupContext $context): bool
	{
		return ($this->condition)($context);
	}

}
