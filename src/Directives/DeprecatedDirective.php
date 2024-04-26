<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Directives;

use WebChemistry\Setup\Directive;

final class DeprecatedDirective extends Directive
{

	public function __construct(
		mixed $value,
		private string $comment = '',
	)
	{
		parent::__construct($value);

		$this->metadata->add('comment', trim(sprintf('@deprecated %s', $this->comment)));
	}

}
