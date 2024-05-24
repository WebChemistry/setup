<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use InvalidArgumentException;
use WebChemistry\Setup\Block;
use WebChemistry\Setup\Block\SectionBlock;
use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\Helper\StringCaseHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\Setup;
use WebChemistry\Setup\SetupCallables;

final class DotEnvLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'dotenv';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(Setup $setup, array $options = []): string
	{
		$builder = new ContentBuilder();

		$prefix = $options['prefix'] ?? '';

		if (!is_string($prefix)) {
			throw new InvalidArgumentException('Prefix must be a string.');
		}

		$setup->getVariables()->forEach(
			fn (string|int|float|bool $value, array $path) => $this->value($builder, $value, $path, $prefix),
			new SetupCallables(
				onDirective: fn (Directive $directive) => $this->directive($builder, $directive),
				onStartBlock: fn (Block $block) => $this->blockStart($builder, $block),
				onEndBlock: fn (Block $block) => $this->blockEnd($builder, $block),
			),
		);

		return trim($builder->getContent());
	}

	protected function blockStart(ContentBuilder $builder, Block $block): void
	{
		if ($block instanceof SectionBlock) {
			SectionBlock::startPrint($builder, $block, '#');
		}
	}

	protected function blockEnd(ContentBuilder $builder, Block $block): void
	{
		if ($block instanceof SectionBlock) {
			SectionBlock::endPrint($builder, $block, '#');
		}
	}

	/**
	 * @param Directive<mixed> $directive
	 */
	protected function directive(ContentBuilder $builder, Directive $directive): void
	{
		$metadata = $directive->getMetadata();

		foreach ($metadata->getScalar('comment') as $comment) {
			$builder->comment((string) $comment);
		}
	}

	/**
	 * @param string[] $path
	 */
	protected function value(ContentBuilder $builder, string|int|float|bool $value, array $path, string $prefix): void
	{
		BuilderHelper::flushCustomLineComments($builder, '# ');
		$name = $prefix . implode('_', array_map(fn (string $str) => strtoupper(StringCaseHelper::camelToUnderscore($str)), $path));

		$val = var_export($value, true);

		if ($val === 'NULL') {
			$val = "''";
		} else if ($val === 'true') {
			$val = '1';
		} else if ($val === 'false') {
			$val = '0';
		}

		$builder->ln(sprintf('%s=%s', $name, $val));
	}

}
