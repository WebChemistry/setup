<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use LogicException;
use Stringable;
use WebChemistry\Setup\Special\Reference;

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
		$values = $this->processValues($this->values);

		$this->walk($builder, $foreach, $values);
	}

	/**
	 * @param callable(string|int|float|bool $value, VariablePath $path): void $foreach
	 * @param mixed[] $values
	 * @param string[] $path
	 */
	private function walk(ContentBuilder $builder, callable $foreach, array $values, array $path = []): void
	{
		foreach ($values as $key => $value) {
			if (is_array($value)) {
				$this->walk($builder, $foreach, $value, [...$path, $key]);
			} else if (is_scalar($value)) {
				$foreach($value, new VariablePath([...$path, $key]));
			} else if ($value instanceof TemplateValue) {
				$value->call($builder);
			} else if ($value !== null) {
				throw new LogicException(sprintf('Value at %s is not scalar.', implode(' > ', ['root', ...$path])));
			}
		}
	}

	/**
	 * @param mixed[] $values
	 * @param string[] $path
	 * @param mixed[] $return
	 * @return mixed[]
	 */
	private function processValues(array $values, array $path = [], array &$return = []): array
	{
		$builderIndex = 0;
		$hasReference = false;

		foreach ($values as $key => $value) {
			$newPath = [...$path, $key];

			if ($value instanceof Directive) {
				$value = $this->processDirectives($key, $value);
			}

			if ($value === null) {
				$return[$key] = $value;
			} else if (is_array($value)) {
				$return[$key] = $this->processValues($value, $newPath);
			} else if ($value instanceof FlattenValue) {
				$this->processValues($value->values, $path, $return);
			} else if ($value instanceof TemplateValue) {
				$return['$builder$' . $builderIndex++] = $value;
			} else {
				if (is_object($value)) {
					if ($value instanceof Stringable) {
						$return[$key] = (string) $value;
					} else if ($value instanceof Reference) {
						$hasReference = true;
						$return[$key] = $value;
					} else {
						throw new LogicException(sprintf('Value at %s is not scalar.', implode(' > ', ['root', ...$newPath])));
					}

					continue;
				}

				if (!is_scalar($value)) {
					throw new LogicException(sprintf('Value at %s is not scalar.', implode(' > ', ['root', ...$newPath])));
				}

				$return[$key] = $value;
			}
		}

		if ($hasReference) {
			foreach ($return as $key => $value) {
				if ($value instanceof Reference) {
					if (!array_key_exists($value->name, $return)) {
						throw new LogicException(sprintf('Reference %s is not defined.', $value->name));
					}

					$return[$key] = $value->process($return[$value->name]);
				}
			}
		}

		return $return;
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
