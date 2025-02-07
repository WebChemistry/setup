<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use InvalidArgumentException;
use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\LanguageGenerator;
use WebChemistry\Setup\SetupValues;
use WebChemistry\Setup\VariablePath;

final class DotEnvLanguageGenerator implements LanguageGenerator
{

	public function getLanguage(): string
	{
		return 'dotenv';
	}

	/**
	 * @param mixed[] $options
	 */
	public function generate(SetupValues $values, array $options = []): string
	{
		$builder = new ContentBuilder('#');

		$prefix = $options['prefix'] ?? '';

		if (!is_string($prefix)) {
			throw new InvalidArgumentException('Prefix must be a string.');
		}

		$values->forEach(
			$builder,
			fn (string|int|float|bool $value, VariablePath $path) => $this->value($builder, $value, $path, $prefix),
		);

		return trim($builder->getContent());
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, VariablePath $path, string $prefix): void
	{
		BuilderHelper::flushCustomLineComments($builder, '# ');
		$name = strtoupper($prefix . $path->toString('_', '__'));

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
