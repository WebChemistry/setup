<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use LogicException;
use Stringable;

final class SetupValues
{

	/**
	 * @param mixed[] $values
	 */
	public function __construct(
		private array $values,
		private SetupContext $context,
	)
	{
	}

	/**
	 * @param callable(string|int|float|bool $value, VariablePath $path): void $foreach
	 */
	public function forEach(ContentBuilder $builder, callable $foreach): void
	{
		$this->_forEach($builder, $foreach, $this->values);
	}

	/**
	 * @param callable(string|int|float|bool $value, VariablePath $path): void $foreach
	 * @param mixed[] $values
	 * @param string[] $path
	 */
	private function _forEach(ContentBuilder $builder, callable $foreach, array $values, array $path = []): void
	{
		foreach ($values as $key => $value) {
			$newPath = [...$path, $key];

			if ($value instanceof Directive) {
				$value = $this->processDirectives($key, $value);
			}

			if ($value === null) {
				continue;
			}

			if (is_array($value)) {
				$this->_forEach($builder, $foreach, $value, $newPath);
			} else if ($value instanceof FlattenValue) {
				$this->_forEach($builder, $foreach, $value->values, $path);
			} else if ($value instanceof TemplateValue) {
				$value->call($builder);
			} else {
				if (is_object($value)) {
					if ($value instanceof Stringable) {
						$value = (string) $value;
					} else {
						throw new LogicException(sprintf('Value at %s is not scalar.', implode(' > ', ['root', ...$newPath])));
					}
				}

				if (!is_scalar($value)) {
					throw new LogicException(sprintf('Value at %s is not scalar.', implode(' > ', ['root', ...$newPath])));
				}

				$foreach($value, new VariablePath($newPath));
			}
		}
	}

	/**
	 * @param Directive<mixed> $directive
	 * @return FlattenValue<mixed>|null
	 */
	private function processDirectives(string $key, Directive $directive): ?FlattenValue
	{
		if (!$directive->isCorrect($this->context)) {
			return null;
		}

		$values = [
			...$directive->before(),
			...$directive->getValues($key),
			...$directive->after(),
		];

		if (!$values) {
			return null;
		}

		return new FlattenValue($values);
	}

}
