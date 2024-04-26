<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use InvalidArgumentException;
use Nette\Utils\FileSystem;
use WebChemistry\Setup\Generators\CssLanguageGenerator;
use WebChemistry\Setup\Generators\JsLanguageGenerator;
use WebChemistry\Setup\Generators\PhpLanguageGenerator;
use WebChemistry\Setup\Generators\ScssLanguageGenerator;

final class Worker
{

	/** @var LanguageGenerator[] */
	private array $generators;

	public function __construct()
	{
		$this->generators = [
			new CssLanguageGenerator(),
			new ScssLanguageGenerator(),
			new PhpLanguageGenerator(),
			new JsLanguageGenerator(),
		];
	}

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
	 * @param callable(SetupOutput $output): (callable(Setup $setup, SetupContext $context): void)  $callback
	 */
	public function run(callable $callback): void
	{
		$setupOutput = new SetupOutput();

		$fn = $callback($setupOutput);

		foreach ($setupOutput->getOutputs() as $output) {
			$generator = $this->getGenerator($output['language']);

			$fn($setup = new Setup(), new SetupContext($output['language']));

			FileSystem::write($output['output'], $generator->generate($setup, $output['options']));
		}
	}

	public function runFile(string $file): void
	{
		$this->run(require $file);
	}

	private function getGenerator(string $language): LanguageGenerator
	{
		foreach ($this->generators as $generator) {
			if ($generator->getLanguage() === $language) {
				return $generator;
			}
		}

		throw new InvalidArgumentException(sprintf('Generator for language %s not found.', $language));
	}

}
