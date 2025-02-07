<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\SetupValues;
use WebChemistry\Setup\VariablePath;

class CssLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'css';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(SetupValues $values, array $options = []): string
	{
		$builder = new ContentBuilder();

		$this->start($builder, $options);

		$values->forEach(
			$builder,
			fn (string|int|float|bool $value, VariablePath $path) => $this->value($builder, $value, $path),
		);

		$this->end($builder);

		return $builder->getContent();
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, VariablePath $path): void
	{
		BuilderHelper::flushMultilineComments($builder);

		$name = $path->toString('-', '__');

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
