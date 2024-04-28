<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use InvalidArgumentException;
use Nette\Utils\Strings;
use WebChemistry\Setup\Block;
use WebChemistry\Setup\Block\SectionBlock;
use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\Setup;
use WebChemistry\Setup\SetupCallables;

final class JsLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'js';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(Setup $setup, array $options = []): string
	{
		$builder = new ContentBuilder();

		$builder->ln('/* This file is auto-generated. Do not edit! */', 2);

		if (!is_string($name = $options['name'] ?? null)) {
			throw new InvalidArgumentException('Missing name.');
		}

		$builder->ln(sprintf('export const %s = {', $name));
		$builder->increaseLevel();

		$setup->getVariables()->forEach(
			fn (string|int|float|bool $value, array $path) => $this->value($builder, $value, $path),
			new SetupCallables(
				onDirective: fn (Directive $directive) => $this->directive($builder, $directive),
				onStartBlock: fn (Block $block) => $this->blockStart($builder, $block),
				onEndBlock: fn (Block $block) => $this->blockEnd($builder, $block),
			),
		);

		$builder->decreaseLevel();
		$builder->ln('};');

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
		$name = implode('', array_map(Strings::firstUpper(...), $path));

		$builder->getCommentsAndFlush();
		$builder->ln(sprintf('%s: %s,', $name, var_export($value, true)));
	}

}
