<?php

namespace Vleap\Warps;

use Exception;
use Illuminate\Support\Collection;
use Vleap\Warps\Actions\IWarpAction;
use Vleap\Warps\Transformers\WarpTransformer;

class WarpBuilder
{
    public string $protocol;
    public string $name;
    public string $title;
    public ?string $description;
    public string $preview;
    /** @var Collection<IWarpAction> */
    public Collection $actions;

    const LATEST_PROTOCOL_IDENTIFIER = 'warp:0.1.0';

    public function __construct()
    {
        $this->protocol = self::LATEST_PROTOCOL_IDENTIFIER;
        $this->actions = new Collection;
    }

    public static function createFromRaw(array $data): Warp
    {
        return WarpTransformer::fromArray($data);
    }

    public function setProtocol(string $protocol): WarpBuilder
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function setName(string $name): WarpBuilder
    {
        $this->name = $name;

        return $this;
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
        $this->ensureIsSet($this->name, 'name is required');
        $this->ensureIsSet($this->title, 'title is required');
        $this->ensureIsSet($this->preview, 'preview is required');

        return new Warp($this->protocol, $this->name, $this->title, $this->description, $this->preview, $this->actions);
    }

    private function ensureIsSet(string $value, string $message): void
    {
        if (empty($value)) throw new Exception($message);
    }
}
