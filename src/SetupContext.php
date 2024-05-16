<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class SetupContext
{

	public function __construct(
		public readonly string $language,
		public readonly string|null $id,
	)
	{
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
