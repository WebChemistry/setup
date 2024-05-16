<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use WebChemistry\Setup\Block\SectionBlock;
use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\Helper\StringCaseHelper;
use WebChemistry\Setup\Block;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\Setup;
use WebChemistry\Setup\SetupCallables;

class CssLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'css';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(Setup $setup, array $options = []): string
	{
		$builder = new ContentBuilder();

		$this->start($builder, $options);

		$setup->getVariables()->forEach(
			fn (string|int|float|bool $value, array $path) => $this->value($builder, $value, $path),
			new SetupCallables(
				onDirective: fn (Directive $directive) => $this->directive($builder, $directive),
				onStartBlock: fn (Block $block) => $this->blockStart($builder, $block),
				onEndBlock: fn (Block $block) => $this->blockEnd($builder, $block),
			),
		);

		$this->end($builder);

		return $builder->getContent();
	}

	protected function blockStart(ContentBuilder $builder, Block $block): void
	{
		if ($block instanceof SectionBlock) {
			SectionBlock::startPrint($builder, $block);
		}
	}

	protected function blockEnd(ContentBuilder $builder, Block $block): void
	{
		if ($block instanceof SectionBlock) {
			SectionBlock::endPrint($builder, $block);
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
	protected function value(ContentBuilder $builder, string|int|float|bool $value, array $path): void
	{
		BuilderHelper::flushMultilineComments($builder);
		$name = implode('-', array_map(StringCaseHelper::camelToDash(...), $path));

		$builder->getCommentsAndFlush();
		$builder->ln(sprintf('--%s: %s;', $name, $value));
	}

	/**
	 * @param mixed[] $options
	 */
	protected function start(ContentBuilder $builder, array $options): void
	{
		$selector = $options['selector'] ?? null;
		$selector = is_string($selector) ? $selector : ':root';

		$builder->ln($selector . ' {');
		$builder->increaseLevel();
	}

	protected function end(ContentBuilder $builder): void
	{
		$builder->decreaseLevel();
		$builder->ln('}');
	}

}
