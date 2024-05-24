<?php declare(strict_types = 1);

namespace WebChemistry\Setup\Block;

use WebChemistry\Setup\Block;
use WebChemistry\Setup\ContentBuilder;

final class SectionBlock implements Block
{

	private bool $end = false;

	public function __construct(
		public readonly string $name,
	)
	{
	}

	public function isEnd(): bool
	{
		return $this->end;
	}

	public function isCorrectEnd(Block $block): bool
	{
		if ($block instanceof self) {
			return $block->end;
		}

		return false;
	}

	public static function end(): self
	{
		$self = new self('');
		$self->end = true;

		return $self;
	}

	/**
	 * @template TValue
	 * @param array<TValue> $values
	 * @return array<TValue|SectionBlock>
	 */
	public static function block(string $name, array $values): array
	{
		return [
			new self($name),
			...$values,
			self::end(),
		];
	}

	public static function startPrint(ContentBuilder $builder, self $block, string $comment = '//'): void
	{
		$builder->forceNewLine(2);
		$builder->ln(sprintf('%s --- %s --- %s', $comment, $block->name, $comment));
	}

	public static function endPrint(ContentBuilder $builder, self $block, string $comment = '//'): void
	{
		$builder->forceNewLine(1);
		$builder->ln(sprintf('%s --- / %s --- %s', $comment, $block->name, $comment));
		$builder->newLine(2);
	}

}
