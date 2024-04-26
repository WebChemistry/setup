<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

interface LanguageGenerator
{

	public function getLanguage(): string;

	/**
	 * @param mixed[] $options
	 */
	public function generate(Setup $setup, array $options = []): string;

}
