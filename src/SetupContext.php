<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use WebChemistry\Setup\Condition\AndCondition;
use WebChemistry\Setup\Condition\ConditionType;
use WebChemistry\Setup\Condition\ContainsGroupCondition;
use WebChemistry\Setup\Condition\ContainsLangCondition;
use WebChemistry\Setup\Condition\OrCondition;
use WebChemistry\Setup\Condition\SetupCondition;

final class SetupContext
{

	/**
	 * @param string[] $groups
	 */
	public function __construct(
		public readonly string $language,
		public readonly string|null $id,
		public readonly array $groups,
	)
	{
	}

	/**
	 * @template TrueKey of array-key
	 * @template TrueValue of mixed
	 * @template FalseKey of array-key
	 * @template FalseValue of mixed
	 * @param SetupCondition $condition
	 * @param array<TrueKey, TrueValue> $true
	 * @param array<FalseKey, FalseValue> $false
	 * @return array<TrueKey, TrueValue>|array<FalseKey, FalseValue>
	 */
	public function if(SetupCondition $condition, array $true, array $false = []): array
	{
		return $condition->execute($this) ? $true : $false;
	}

	/**
	 * @template TrueKey of array-key
	 * @template TrueValue of mixed
	 * @template FalseKey of array-key
	 * @template FalseValue of mixed
	 * @param SetupCondition $condition
	 * @param array<TrueKey, TrueValue> $true
	 * @param array<FalseKey, FalseValue> $false
	 * @return array<TrueKey, TrueValue>|array<FalseKey, FalseValue>
	 */
	public function ifNot(SetupCondition $condition, array $true, array $false = []): array
	{
		return !$condition->execute($this) ? $true : $false;
	}

	/**
	 * @template TrueKey of array-key
	 * @template TrueValue of mixed
	 * @template FalseKey of array-key
	 * @template FalseValue of mixed
	 * @param array<TrueKey, TrueValue> $true
	 * @param array<FalseKey, FalseValue> $false
	 * @return array<TrueKey, TrueValue>|array<FalseKey, FalseValue>
	 */
	public function ternary(bool $value, array $true, array $false = []): array
	{
		return $value ? $true : $false;
	}

	/**
	 * @param string[]|string $groups
	 */
	public function containsGroup(array|string $groups, ConditionType $type = ConditionType::Or): ContainsGroupCondition
	{
		return new ContainsGroupCondition($groups, $type);
	}

	/**
	 * @param string[]|string $langs
	 */
	public function containsLang(array|string $langs): ContainsLangCondition
	{
		return new ContainsLangCondition($langs);
	}

	public function or(SetupCondition ...$conditions): OrCondition
	{
		return new OrCondition(...$conditions);
	}

	public function and(SetupCondition ...$conditions): AndCondition
	{
		return new AndCondition(...$conditions);
	}

	/**
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function skipIf(bool $condition, array $values): array
	{
		return !$condition ? $values : [];
	}

	/**
	 * @param string[] $groups
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function onlyForGroup(array $groups, array $values): array
	{
		$contains = false;

		foreach ($groups as $group) {
			if (in_array($group, $this->groups, true)) {
				$contains = true;

				break;
			}
		}

		return $contains ? $values : [];
	}

	/**
	 * @param string[] $groups
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function skipIfGroup(array $groups, array $values): array
	{
		$contains = false;

		foreach ($groups as $group) {
			if (in_array($group, $this->groups, true)) {
				$contains = true;

				break;
			}
		}

		return $contains ? [] : $values;
	}

	/**
	 * @param string[] $languages
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function onlyForLang(array $languages, array $values): array
	{
		return in_array($this->language, $languages, true) ? $values : [];
	}

	/**
	 * @param string[] $languages
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function skipIfLang(array $languages, array $values): array
	{
		return in_array($this->language, $languages, true) ? [] : $values;
	}

	/**
	 * @param string[] $ids
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function onlyForId(array $ids, array $values): array
	{
		return in_array($this->id, $ids, true) ? $values : [];
	}

	/**
	 * @param string[] $ids
	 * @param mixed[] $values
	 * @return mixed[]
	 */
	public function skipIfId(array $ids, array $values): array
	{
		return in_array($this->id, $ids, true) ? [] : $values;
	}

}
