<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Condition;

use WebChemistry\Setup\SetupContext;

final class AndCondition implements SetupCondition
{

	/** @var SetupCondition[] */
	private array $conditions;

	public function __construct(SetupCondition ...$conditions)
	{
		$this->conditions = $conditions;
	}

	public function execute(SetupContext $context): bool
	{
		foreach ($this->conditions as $condition) {
			if (!$condition->execute($context)) {
				return false;
			}
		}

		return true;
	}

}
