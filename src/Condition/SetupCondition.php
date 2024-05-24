<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Condition;

use WebChemistry\Setup\SetupContext;

interface SetupCondition
{

	public function execute(SetupContext $context): bool;

}
