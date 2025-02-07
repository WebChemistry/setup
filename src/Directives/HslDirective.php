<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Directives;

use WebChemistry\Setup\Directive;
use WebChemistry\Setup\FlattenValue;
use WebChemistry\Setup\UI\Color;

/**
 * @extends Directive<Color>
 */
final class HslDirective extends Directive
{

	public function __construct(Color|string $value)
	{
		parent::__construct(is_string($value) ? Color::from($value) : $value);
	}

	public function getValues(string $key): array
	{
		[$h, $s, $l] = $this->value->toHslValues();

		return [
			$key => $this->value,
			$this->modifier($key, 'h') => $h,
			$this->modifier($key, 's') => sprintf('%d%%', $s),
			$this->modifier($key, 'l') => sprintf('%d%%', $l),
		];
	}

}
