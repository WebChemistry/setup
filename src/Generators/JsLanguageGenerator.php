<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use InvalidArgumentException;
use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\SetupValues;
use WebChemistry\Setup\VariablePath;

final class JsLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'js';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(SetupValues $values, array $options = []): string
	{
		$builder = new ContentBuilder();

		$builder->ln('/* This file is auto-generated. Do not edit! */', 2);

		if (($options['type'] ?? null) !== 'commonjs') {
			if (!is_string($name = $options['name'] ?? null)) {
				throw new InvalidArgumentException('Missing name.');
			}

			$builder->ln(sprintf('export const %s = {', $name));
		} else {
			$builder->ln('module.exports = {');
		}

		$builder->increaseLevel();

		$values->forEach(
			$builder,
			fn (string|int|float|bool $value, VariablePath $path) => $this->value($builder, $value, $path),
		);

		$builder->decreaseLevel();
		$builder->ln('};');

		return $builder->getContent();
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, VariablePath $path): void
	{
		BuilderHelper::flushMultilineComments($builder);
		$name = $path->toString('', '__');

		$builder->ln(sprintf('%s: %s,', $name, var_export($value, true)));
	}

}
