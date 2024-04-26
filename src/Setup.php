<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class Setup
{

	public readonly SetupDirectives $directives;

	/** @var mixed[] */
	private array $variables = [];

	public function __construct()
	{
		$this->directives = new SetupDirectives();
	}

	/**
	 * @param mixed[] $variables
	 */
	public function setVariables(array $variables): self
	{
		$this->variables = $variables;

		return $this;
	}

	public function getVariables(): SetupValues
	{
		return new SetupValues($this->variables);
	}

}
