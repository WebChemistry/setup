<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Condition;

use WebChemistry\Setup\SetupContext;

final class ContainsLangCondition implements SetupCondition
{

	/** @var string[] */
	private array $languages;

	/**
	 * @param string[]|string $languages
	 */
	public function __construct(string|array $languages)
	{
		$this->languages = is_array($languages) ? $languages : [$languages];
	}

	public function execute(SetupContext $context): bool
	{
		return in_array($context->language, $this->languages, true);
	}

}
