<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use InvalidArgumentException;
use LogicException;
use Nette\Utils\FileSystem;
use WebChemistry\Setup\Generators\CssLanguageGenerator;
use WebChemistry\Setup\Generators\DotEnvLanguageGenerator;
use WebChemistry\Setup\Generators\JsLanguageGenerator;
use WebChemistry\Setup\Generators\PhpLanguageGenerator;
use WebChemistry\Setup\Generators\ScssVarListLanguageGenerator;
use WebChemistry\Setup\Generators\ScssLanguageGenerator;
use WebChemistry\Setup\Helper\SetupHelper;
use WebChemistry\Setup\Variant\Variants;

final class Setup
{

	public readonly SetupDirectives $directives;

	public readonly SetupHelper $helper;

	public readonly Variants $variants;

	private bool $running = false;

	/** @var array{ language: string, output: string, options: mixed[], id: string|null, groups: string[] }[] */
	private array $outputs = [];

	/** @var LanguageGenerator[] */
	private array $generators;

	/** @var array{ name: string, values: mixed, condition: (callable(SetupContext $context): bool)|null }[] */
	private array $variables = [];

	public function __construct(callable $onRun)
	{
		$this->directives = new SetupDirectives();
		$this->helper = new SetupHelper();
		$this->variants = new Variants();
		$this->generators = [
			new CssLanguageGenerator(),
			new ScssLanguageGenerator(),
			new PhpLanguageGenerator(),
			new JsLanguageGenerator(),
			new DotEnvLanguageGenerator(),
			new ScssVarListLanguageGenerator(),
		];

		$onRun($this->work(...));
	}

	private function work(): void
	{
		$this->running = true;

		foreach ($this->outputs as $output) {
			$generator = $this->getGenerator($output['language']);
			$context = new SetupContext($output['language'], $output['id'], $output['groups']);
			$variables = [];
			$processedVariables = [];

			foreach ($this->variables as $variable) {
				if (isset($variable['condition'])) {
					if ($variable['condition']($context) === false) {
						continue;
					}
				}

				if (isset($processedVariables[$variable['name']])) {
					throw new LogicException(sprintf('Variable %s meets condition multiple times for context %s.', $variable['name'], $context->toString()));
				}

				$variables[$variable['name']] = $variable['values'];
				$processedVariables[$variable['name']] = true;
			}

			FileSystem::write($output['output'], $generator->generate(new SetupValues($variables, $context), $output['options']));
		}

		$this->running = false;
	}

	/**
	 * @param mixed[] $values
	 * @param (callable(SetupContext $context): bool)|null $condition
	 * @return self
	 */
	public function variable(string $name, array $values, ?callable $condition = null, ?string $sectionName = null): self
	{
		$this->check(__METHOD__);

		if ($sectionName) {
			$values = $this->directives->section($values, $sectionName);
		}

		$this->variables[] = [
			'name' => $name,
			'values' => $values,
			'condition' => $condition,
		];

		return $this;
	}

	/**
	 * @param mixed[] $options
	 * @param string[] $groups
	 */
	public function addOutput(string $language, string $output, array $options = [], ?string $id = null, array $groups = []): self
	{
		$this->check(__METHOD__);

		$this->outputs[] = [
			'language' => $language,
			'output' => $output,
			'options' => $options,
			'groups' => $groups,
			'id' => $id,
		];

		return $this;
	}

	private function check(string $method): void
	{
		if ($this->running) {
			throw new LogicException(sprintf('Method %s can be called only before running.', $method));
		}
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
