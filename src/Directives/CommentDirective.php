<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Directives;

use WebChemistry\Setup\ContentBuilder;
use WebChemistry\Setup\Directive;
use WebChemistry\Setup\TemplateValue;

/**
 * @extends Directive<mixed>
 */
final class CommentDirective extends Directive
{

	public function __construct(
		mixed $value,
		private string $comment = '',
	)
	{
		parent::__construct($value);
	}

	public function before(): array
	{
		return [
			new TemplateValue(function (ContentBuilder $builder): void {
				$builder->comment($this->comment);
			}),
		];
	}

}

