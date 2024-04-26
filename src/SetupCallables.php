<?php declare(strict_types = 1);

namespace WebChemistry\Setup;

final class SetupCallables
{

	/** @var (callable(Directive $directive): void)|null */
	private $onDirective;

	/** @var (callable(Block $block): void)|null */
	private $onStartBlock;

	/** @var (callable(Block $block): void)|null */
	private $onEndBlock;

	/**
	 * @param (callable(Directive $directive): void)|null $onDirective
	 * @param (callable(Block $block): void)|null $onStartBlock
	 * @param (callable(Block $block): void)|null $onEndBlock
	 */
	public function __construct(
		?callable $onDirective = null,
		?callable $onStartBlock = null,
		?callable $onEndBlock = null,
	)
	{
		$this->onDirective = $onDirective;
		$this->onStartBlock = $onStartBlock;
		$this->onEndBlock = $onEndBlock;
	}

	/**
	 * @param Directive $directive
	 */
	public function callOnDirective(Directive $directive): void
	{
		if ($this->onDirective) {
			($this->onDirective)($directive);
		}
	}

	/**
	 * @param Block $block
	 */
	public function callOnStartBlock(Block $block): void
	{
		if ($this->onStartBlock) {
			($this->onStartBlock)($block);
		}
	}

	/**
	 * @param Block $block
	 */
	public function callOnEndBlock(Block $block): void
	{
		if ($this->onEndBlock) {
			($this->onEndBlock)($block);
		}
	}

}
