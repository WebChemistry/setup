<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use InvalidArgumentException;
use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\SetupValues;
use WebChemistry\Setup\VariablePath;

final class PhpLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'php';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(SetupValues $values, array $options = []): string
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

		$values->forEach(
			$builder,
			fn (string|int|float|bool $value, VariablePath $path) => $this->value($builder, $value, $path),
		);

		$builder->decreaseLevel();
		$builder->ln('}');

		return $builder->getContent();
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, VariablePath $path): void
	{
		BuilderHelper::flushMultilineComments($builder);
		$name = $path->toString('', '__');

		$builder->ln(sprintf('public const %s = %s;', $name, var_export($value, true)));
	}

}
