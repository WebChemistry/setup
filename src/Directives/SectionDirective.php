<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Directives;

use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\TemplateValue;

/**
 * @extends Directive<mixed[]>
 */
final class SectionDirective extends Directive
{

	/**
	 * @param mixed[] $values
	 */
	public function __construct(
		array $values,
		private string $name,
	)
	{
		parent::__construct($values);
	}

	public function before(): array
	{
		return [
			new TemplateValue(function (ContentBuilder $builder): void {
				$builder->forceNewLine(2);
				$builder->inlineComment(sprintf('--- %s ---', $this->name));
			}),
		];
	}

	public function after(): array
	{
		return [
			new TemplateValue(function (ContentBuilder $builder): void {
				$builder->forceNewLine(1);
				$builder->inlineComment(sprintf('--- / %s ---', $this->name));
				$builder->newLine(2);
			}),
		];
	}

}
