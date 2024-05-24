<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class SetupOutput
{

	/** @var array{ language: string, output: string, options: mixed[], id: string|null, groups: string[] }[] */
	private array $outputs = [];

	/**
	 * @return array{ language: string, output: string, options: mixed[], id: string|null, groups: string[] }[]
	 */
	public function getOutputs(): array
	{
		return $this->outputs;
	}

	/**
	 * @param mixed[] $options
	 * @param string[] $groups
	 */
	public function addOutput(string $language, string $output, array $options = [], ?string $id = null, array $groups = []): self
	{
		$this->outputs[] = [
			'language' => $language,
			'output' => $output,
			'options' => $options,
			'groups' => $groups,
			'id' => $id,
		];

		return $this;
	}

}
