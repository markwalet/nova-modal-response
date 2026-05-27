<?php

namespace Markwalet\NovaModalResponse\Blocks;

class ViewBlock extends Block
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private readonly string $view, private readonly array $data = []) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => 'html',
            'value' => view($this->view, $this->data)->render(),
        ];
    }
}
