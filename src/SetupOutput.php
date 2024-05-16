<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class SetupOutput
{

	/** @var array{ language: string, output: string, options: mixed[], id: string|null }[] */
	private array $outputs = [];

	/**
	 * @return array{ language: string, output: string, options: mixed[], id: string|null }[]
	 */
	public function getOutputs(): array
	{
		return $this->outputs;
	}

	/**
	 * @param mixed[] $options
	 */
	public function addOutput(string $language, string $output, array $options = [], ?string $id = null): self
	{
		$this->outputs[] = [
			'language' => $language,
			'output' => $output,
			'options' => $options,
			'id' => $id,
		];

		return $this;
	}

}
