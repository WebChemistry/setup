<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\VariablePath;

final class ScssLanguageGenerator extends CssLanguageGenerator
{

	public function getLanguage(): string
	{
		return 'scss';
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, VariablePath $path): void
	{
		BuilderHelper::flushMultilineComments($builder);
		$name = $path->toString('-', '__');

		$builder->ln(sprintf('$%s: %s;', $name, $value));
	}

	/**
	 * @param mixed[] $options
	 */
	protected function start(ContentBuilder $builder, array $options): void
	{
	}

	protected function end(ContentBuilder $builder): void
	{
	}

}
