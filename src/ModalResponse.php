<?php

namespace Markwalet\NovaModalResponse;

use Closure;
use Illuminate\Support\Stringable;
use InvalidArgumentException;
use JsonException;
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
     * @param array<int, Block|string|Stringable>|Closure $blocks
     */
    public static function stack(array|Closure $blocks): self
    {
        $response = new self;
        $resolved = $blocks instanceof Closure ? $blocks() : $blocks;

        if (! is_array($resolved)) {
            throw new InvalidArgumentException('Stack closure must return an array of Block instances.');
        }

        $response->blocks = array_values(array_map(Block::normalize(...), $resolved));
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

    /**
     * @param array<string, mixed> $data
     */
    public static function view(string $view, array $data = []): self
    {
        return self::stack([Block::view($view, $data)]);
    }

    public static function code(string|Stringable $value): self
    {
        return self::stack([Block::code($value)]);
    }

    /**
     * @param array<mixed> $value
     *
     * @throws JsonException
     */
    public static function json(array $value): self
    {
        return self::stack([Block::json($value)]);
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

    public function withoutSyntaxHighlighting(): self
    {
        $this->withoutHighlight = true;
        $this->refreshModal();

        return $this;
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
