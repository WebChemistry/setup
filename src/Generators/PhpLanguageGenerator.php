<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use InvalidArgumentException;
use LogicException;
use Nette\Utils\Strings;
use WebChemistry\Setup\Block;
use WebChemistry\Setup\Block\SectionBlock;
use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\Helper\StringCaseHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\Setup;
use WebChemistry\Setup\SetupCallables;

final class PhpLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'php';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(Setup $setup, array $options = []): string
	{
		$builder = new ContentBuilder();

		$builder->ln('<?php declare(strict_types = 1);', 2);
		$builder->ln('/* This file is auto-generated. Do not edit! */', 2);

		if (is_string($namespace = $options['namespace'] ?? null)) {
			$builder->ln(sprintf('namespace %s;', $namespace), 2);
		}

		if (!is_string($class = $options['name'] ?? null)) {
			throw new InvalidArgumentException('Missing class name.');
		}

		$builder->ln(sprintf('final class %s', $class));
		$builder->ln('{');
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
		$builder->ln('}');

		return $builder->getContent();
	}

	protected function blockStart(ContentBuilder $builder, Block $block): void
	{
		if ($block instanceof SectionBlock) {
			$builder->ln(sprintf('/*** %s ***/', $block->name));
		}
	}

	protected function blockEnd(ContentBuilder $builder, Block $block): void
	{
		if ($block instanceof SectionBlock) {
			$builder->ln(sprintf('/*** / %s ***/', $block->name));
		}
	}

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
		$builder->ln(sprintf('public const %s = %s;', $name, var_export($value, true)));
	}

}