<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Condition;

use WebChemistry\Setup\SetupContext;

final class ContainsGroupCondition implements SetupCondition
{

	/** @var string[] */
	private array $groups;

	/**
	 * @param string[]|string $groups
	 */
	public function __construct(
		array|string $groups,
		private ConditionType $type = ConditionType::Or,
	)
	{
		$this->groups = is_string($groups) ? [$groups] : $groups;
	}

	public function execute(SetupContext $context): bool
	{
		if ($this->type === ConditionType::Or) {
			foreach ($this->groups as $group) {
				if (in_array($group, $context->groups, true)) {
					return true;
				}
			}

			return false;
		}

		foreach ($this->groups as $group) {
			if (!in_array($group, $context->groups, true)) {
				return false;
			}
		}

		return true;
	}

}
