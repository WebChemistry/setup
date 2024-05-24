<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Condition;

use WebChemistry\Setup\SetupContext;

final class InlineCondition implements SetupCondition
{

	public function __construct(
		private bool $value,
	)
	{
	}

	public function execute(SetupContext $context): bool
	{
		return $this->value;
	}

}
