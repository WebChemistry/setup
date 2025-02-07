<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use Nette\Utils\Strings;

final class VariablePath
{

	/**
	 * @param string[] $path
	 */
	public function __construct(
		private array $path,
	)
	{
	}

	public function toString(string $separator, string $modifierSeparator): string
	{
		if ($separator === '') {
			$fn = Strings::firstUpper(...);
		} else {
			$fn = fn (string $input): string => strtolower(Strings::replace($input, '/([a-zA-Z])(?=[A-Z])/', '$1' . $separator));
		}

		if ($modifierSeparator === '') {
			$modifierFn = Strings::firstUpper(...);
		} else {
			$modifierFn = fn (string $input): string => $input;
		}

		$path = [];

		foreach ($this->path as $value) {
			$value = $fn($value);

			$pos = strpos($value, '#');

			if ($pos !== false) {
				$value = substr($value, 0, $pos) . $modifierSeparator . $modifierFn(substr($value, $pos + 1));
			}

			$path[] = $value;
		}

		return implode($separator, $path);
	}

}
