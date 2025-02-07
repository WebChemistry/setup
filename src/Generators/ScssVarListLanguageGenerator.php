<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\VariablePath;

final class ScssVarListLanguageGenerator extends CssLanguageGenerator
{

	public function getLanguage(): string
	{
		return 'scss-var-list';
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, VariablePath $path): void
	{
		BuilderHelper::flushMultilineComments($builder);
		$name = $path->toString('-', '__');

		$builder->ln('--' . $name);
	}

	/**
	 * @param mixed[] $options
	 */
	protected function start(ContentBuilder $builder, array $options): void
	{
		$builder->ln('$variable-list: (');
		$builder->increaseLevel();
	}

	protected function end(ContentBuilder $builder): void
	{
		$builder->decreaseLevel();
		$builder->ln(');');
	}

}
