<?php

namespace Markwalet\NovaModalResponse;

use Closure;
use Illuminate\Support\Stringable;
use InvalidArgumentException;
use Laravel\Nova\Actions\ActionResponse;
use Markwalet\NovaModalResponse\Blocks\Block;

class ModalResponse extends ActionResponse
{
    /** @var array<int, Block> */
    private array $blocks = [];

    /** @var array<string, mixed> */
    private array $chrome = [];

    private bool $withoutHighlight = false;

    /**
     * @param array<int, Block>|Closure $blocks
     */
    public static function stack(array|Closure $blocks): self
    {
        $response = new self;
        $resolved = $blocks instanceof Closure ? $blocks() : $blocks;

        if (! is_array($resolved)) {
            throw new InvalidArgumentException('Stack closure must return an array of Block instances.');
        }

        foreach ($resolved as $block) {
            if (! $block instanceof Block) {
                throw new InvalidArgumentException('Stack must contain only Block instances.');
            }
        }

        $response->blocks = array_values($resolved);
        $response->refreshModal();

        return $response;
    }

    public static function text(string|Stringable $value): self
    {
        return self::stack([Block::text($value)]);
    }

    public static function html(string|Stringable $value): self
    {
        return self::stack([Block::html($value)]);
    }

    public static function code(string|Stringable $value): self
    {
        return self::stack([Block::code($value)]);
    }

    public function title(string $title): self
    {
        return $this->setChrome('title', $title);
    }

    public function size(string $size): self
    {
        return $this->setChrome('size', $size);
    }

    public function closeButton(string $label): self
    {
        return $this->setChrome('closeButtonText', $label);
    }

    private function setChrome(string $key, mixed $value): self
    {
        $this->chrome[$key] = $value;
        $this->refreshModal();

        return $this;
    }

    private function refreshModal(): void
    {
        $payload = (new PayloadBuilder)->build($this->blocks, $this->chrome, $this->withoutHighlight);

        $this->withModal('modal-response', $payload);
    }
}
