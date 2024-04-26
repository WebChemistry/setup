<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use LogicException;
use Stringable;

final class SetupValues
{

	/** @var Block[] */
	private array $blockStack = [];

	/**
	 * @param mixed[] $values
	 */
	public function __construct(
		private array $values,
	)
	{
	}

	/**
	 * @param callable(string|int|float|bool $value, string[] $path): void $foreach
	 */
	public function forEach(callable $foreach, SetupCallables $callables): void
	{
		$this->blockStack = [];

		$this->_forEach($foreach, $this->values, $callables);
	}

	/**
	 * @param callable(string|int|float|bool $value, string[] $path): void $foreach
	 * @param mixed[] $values
	 * @param string[] $path
	 */
	private function _forEach(callable $foreach, array $values, SetupCallables $callables, array $path = []): void
	{
		foreach ($values as $key => $value) {
			$newPath = [...$path, $key];

			if ($value instanceof Block) {
				if ($value->isEnd()) {
					$lastBlock = array_pop($this->blockStack);
					if (!$lastBlock) {
						throw new LogicException(
							sprintf(
								'Cannot use end block %s without start block at %s.',
								$value::class,
								implode(' > ', ['root', ...$newPath]),
							),
						);
					}

					if (!$lastBlock->isCorrectEnd($value)) {
						throw new LogicException(sprintf('Block %s is not closed at %s.', $lastBlock::class, implode(' > ', ['root', ...$newPath])));
					}

					$callables->callOnEndBlock($lastBlock);

					continue;
				} else {
					$this->blockStack[] = $value;

					$callables->callOnStartBlock($value);
				}

				continue;
			}

			if ($value instanceof Directive) {
				$value = $this->processDirectives($value, $callables);
			}

			if ($value === null) {
				continue;
			}


			if (is_array($value)) {
				$this->_forEach($foreach, $value, $callables, $newPath);
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

				$foreach($value, $newPath);
			}
		}
	}

	private function processDirectives(Directive $directive, SetupCallables $callables): mixed
	{
		$value = $directive->getValue();

		if ($value === null) {
			return null;
		}

		foreach ($directive->getDirectives() as $directive) {
			$callables->callOnDirective($directive);
		}

		return $value;
	}

}
