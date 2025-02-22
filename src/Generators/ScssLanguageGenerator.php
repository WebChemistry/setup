<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Generators;

use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Helper\BuilderHelper;
use WebChemistry\Setup\SetupValues;
use WebChemistry\Setup\VariablePath;

final class ScssLanguageGenerator extends CssLanguageGenerator
{

	private string $format = 'classic';

	public function getLanguage(): string
	{
		return 'scss';
	}

	public function generate(SetupValues $values, array $options = []): string
	{
		$this->format = $options['format'] ?? 'classic';

		$content = parent::generate($values, $options);

		$this->format = 'classic';

		return $content;
	}

	protected function value(ContentBuilder $builder, string|int|float|bool $value, VariablePath $path): void
	{
		BuilderHelper::flushMultilineComments($builder);
		$name = $path->toString('-', '__');

		if ($this->format === 'map') {
			if (is_string($value) && str_contains($value, ',')) {
				$value = sprintf('"%s"', $value);
			}

			$builder->ln(sprintf('"%s": %s,', $name, $value));
		} else {
			$builder->ln(sprintf('$%s: %s;', $name, $value));
		}
	}

	/**
	 * @param mixed[] $options
	 */
	protected function start(ContentBuilder $builder, array $options): void
	{
		if ($this->format === 'map') {
			$builder->ln('$vars: (');
			$builder->increaseLevel();
		}
	}

	protected function end(ContentBuilder $builder): void
	{
		if ($this->format === 'map') {
			$builder->decreaseLevel();
			$builder->ln(');');
		}
	}

}
