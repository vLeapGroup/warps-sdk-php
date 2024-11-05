<?php

namespace Vleap;

use Exception;
use Illuminate\Support\Collection;
use Vleap\Actions\IWarpAction;
use Vleap\Transformers\WarpTransformer;

class WarpBuilder
{
    public readonly string $name;
    public readonly string $title;
    public readonly ?string $description;
    public readonly string $preview;
    /** @var Collection<IWarpAction> */
    public readonly Collection $actions;

    public function __construct(string $name)
    {
        $this->ensureIsSet($name, 'name is required');

        $this->name = $name;
        $this->actions = new Collection;
    }

    public function setTitle(string $title): WarpBuilder
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription(string $description): WarpBuilder
    {
        $this->description = $description;

        return $this;
    }

    public function setPreview(string $preview): WarpBuilder
    {
        $this->preview = $preview;

        return $this;
    }

    public function addAction(IWarpAction $action): WarpBuilder
    {
        $this->actions->push($action);

        return $this;
    }

    public function build(): Warp
    {
        $this->ensureIsSet($this->title, 'title is required');
        $this->ensureIsSet($this->preview, 'preview is required');

        return new Warp($this->name, $this->title, $this->description, $this->preview, $this->actions);
    }

    private function ensureIsSet(string $value, string $message): void
    {
        if (empty($value)) throw new Exception($message);
    }
}
