<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final readonly class SetupContext
{

	/**
	 * @param string[] $groups
	 */
	public function __construct(
		public string $language,
		public string|null $id,
		public array $groups,
	)
	{
	}

	/**
	 * @param string[]|string $groups
	 */
	public function containsGroup(array|string $groups): bool
	{
		if (is_string($groups)) {
			$groups = [$groups];
		}

		return (bool) array_intersect($this->groups, $groups);
	}

	public function toString(): string
	{
		return sprintf('language: %s, id: %s, groups: %s', $this->language, $this->id ?? '(empty)', implode(', ', $this->groups));
	}

	/**
	 * @param string[] $languages
	 */
	public function notInLanguage(array $languages): bool
	{
		return !in_array($this->language, $languages, true);
	}

}
