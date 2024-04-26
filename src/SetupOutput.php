<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class SetupOutput
{

	/** @var array{ language: string, output: string, options: mixed[] }[] */
	private array $outputs = [];

	/**
	 * @return array{ language: string, output: string, options: mixed[] }[]
	 */
	public function getOutputs(): array
	{
		return $this->outputs;
	}

	/**
	 * @param mixed[] $options
	 */
	public function addOutput(string $language, string $output, array $options = []): self
	{
		$this->outputs[] = [
			'language' => $language,
			'output' => $output,
			'options' => $options,
		];

		return $this;
	}

}
