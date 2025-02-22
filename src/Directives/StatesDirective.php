<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Directives;

use WebChemistry\Setup\Directive;
use WebChemistry\Setup\Special\Reference;
use WebChemistry\Setup\UI\Color;

/**
 * @extends Directive<string|Color|Directive<string|Color>>
 */
final class StatesDirective extends Directive
{

	/**
	 * @param string|Color|Directive<string|Color> $core
	 * @param array<string, string|Color|Reference> $states
	 */
	public function __construct(
		string|Color|Directive $core,
		private array $states,
	)
	{
		parent::__construct($core);
	}

	public function getValues(string $key): array
	{
		$values = parent::getValues($key);

		foreach ($this->states as $name => $value) {
			if ($value instanceof Reference) {
				$value = $value->process($values[$key]);
			}
			
			$values[$this->modifier($key, $name)] = $value;
		}

		return $values;
	}

}
