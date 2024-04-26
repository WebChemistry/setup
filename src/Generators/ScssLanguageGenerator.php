<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\Helper\StringCaseHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\Setup;

final class ScssLanguageGenerator extends CssLanguageGenerator
{

	public function getLanguage(): string
	{
		return 'scss';
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, array $path): void
	{
		BuilderHelper::flushMultilineComments($builder);
		$name = implode('-', array_map(StringCaseHelper::camelToDash(...), $path));

		$builder->getCommentsAndFlush();
		$builder->ln(sprintf('$%s: %s;', $name, $value));
	}

	protected function start(ContentBuilder $builder): void
	{
	}

	protected function end(ContentBuilder $builder): void
	{
	}

}
