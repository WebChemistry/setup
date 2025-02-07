<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class Worker
{

	/**
	 * @param non-empty-list<string> $arguments
	 */
	public static function fromConsole(array $arguments): void
	{
		$script = $arguments[0];
		$file = $arguments[1] ?? null;

		if (!is_string($file) || !$file) {
			echo "Usage: $script <input>\n";

			exit(1);
		}

		if (!is_file($file)) {
			echo "Input file $file not found.\n";

			exit(1);
		}

		if (!str_ends_with($file, '.php')) {
			echo "Input file '$file' must be PHP file.\n";

			exit(1);
		}

		$callable = require $file;

		if (!is_callable($callable)) {
			echo "Input file '$file' must return callable.\n";

			exit(1);
		}

		(new self())->run($callable);
	}

	/**
	 * @param callable(Setup $setup): void  $callback
	 */
	public function run(callable $callback): void
	{
		$callbacks = [];
		$exec = function (callable $fn) use (&$callbacks): void {
			$callbacks[] = $fn;
		};

		$callback(new Setup($exec));

		foreach ($callbacks as $callback) {
			$callback();
		}
	}

	public function runFile(string $file): void
	{
		$this->run(require $file);
	}

}
