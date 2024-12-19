<?php

namespace Markwalet\NovaModalResponse;

use Illuminate\Support\Stringable;
use Laravel\Nova\Actions\ActionResponse;

class ActionModal extends ActionResponse
{
    private bool $highlight = true;
    private string $title = '';
    private string $size = '2xl';
    private string $closeButtonText = 'Close';

    /**
     * Create a modal with a code snippet.
     *
     * @param string|Stringable $snippet
     * @return self
     */
    public static function code(string|Stringable $snippet): self
    {
        return self::modal('modal-response', [
            'code' => $snippet,
        ]);
    }

    /**
     * Create a modal with a json payload.
     *
     * @param array $data
     * @return self
     * @throws \JsonException
     */
    public static function json(array $data): self
    {
        return self::modal('modal-response', [
            'code' => json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        ]);
    }

    /**
     * Create a modal with a single paragraph of text.
     *
     * @param string|Stringable $text
     * @return self
     */
    public static function text(string|Stringable $text): self
    {
        return self::modal('modal-response', [
            'body' => $text,
        ]);
    }

    /**
     * Create a modal with unescaped html.
     *
     * @param string|Stringable $text
     * @return self
     */
    public static function html(string|Stringable $text): self
    {
        return self::modal('modal-response', [
            'html' => $text,
        ]);
    }

    /**
     * Set the title for the modal.
     *
     * @param string $title
     * @return $this
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Adjust the width of the modal.
     *
     * @param string $size
     * @return $this
     */
    public function size(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Disable syntax highlighting for json or code blocks.
     *
     * @return $this
     */
    public function withoutSyntaxHighlighting(): self
    {
        $this->highlight = false;

        return $this;
    }

    /**
     * Adjust the text of the close button.
     *
     * @param string $label
     * @return $this
     */
    public function closeButton(string $label): self
    {
        $this->closeButtonText = $label;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array_merge([
            'modal' => 'modal-response',
            'title' => $this->title,
            'size' => $this->size,
            'highlight' => $this->highlight,
            'closeButtonText' => $this->closeButtonText,
        ], parent::jsonSerialize());
    }
}
