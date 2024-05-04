<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

use WebChemistry\Setup\Helper\SetupHelper;

final class Setup
{

	public readonly SetupDirectives $directives;

	public readonly SetupHelper $helper;

	/** @var mixed[] */
	private array $variables = [];

	public function __construct()
	{
		$this->directives = new SetupDirectives();
		$this->helper = new SetupHelper();
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
