<?php

use Vleap\Warps\WarpAction;
use Vleap\Warps\Actions\ActionType;
use Vleap\Warps\Actions\CollectAction;
use Vleap\Warps\Actions\CollectActionDestinationHttp;
use Vleap\Warps\Transformers\Actions\CollectActionTransformer;

it('transforms a collect action with CollectActionDestinationHttp', function () {
    $action = WarpAction::create('test action')
        ->collect(new CollectActionDestinationHttp('https://vleap.ai', 'POST', ['Authorization' => 'Bearer token']), collect());

    $actual = CollectActionTransformer::toArray($action);

    expect($actual)->toBe([
        'type' => ActionType::Collect->value,
        'label' => 'test action',
        'description' => null,
        'destination' => [
            'url' => 'https://vleap.ai',
            'method' => 'POST',
            'headers' => ['Authorization' => 'Bearer token'],
        ],
        'next' => null,
    ]);
});

it('transforms a collect action with string destination', function () {
    $action = new CollectAction(
        label: 'test action',
        description: null,
        destination: 'https://example.com',
        inputs: collect(),
        next: null,
    );

    $actual = CollectActionTransformer::toArray($action);

    expect($actual)->toBe([
        'type' => ActionType::Collect->value,
        'label' => 'test action',
        'description' => null,
        'destination' => 'https://example.com',
        'next' => null,
    ]);
});

it('transforms a collect action with null destination', function () {
    $action = new CollectAction(
        label: 'test action',
        description: null,
        destination: null,
        inputs: collect(),
        next: null,
    );

    $actual = CollectActionTransformer::toArray($action);

    expect($actual)->toBe([
        'type' => ActionType::Collect->value,
        'label' => 'test action',
        'description' => null,
        'destination' => null,
        'next' => null,
    ]);
});

it('creates a collect action from array with CollectActionDestinationHttp', function () {
    $data = [
        'label' => 'test action',
        'description' => 'test description',
        'destination' => [
            'url' => 'https://vleap.ai',
            'method' => 'POST',
            'headers' => ['Authorization' => 'Bearer token'],
        ],
        'inputs' => [],
        'next' => 'next-action',
    ];

    $action = CollectActionTransformer::fromArray($data);

    expect($action)->toBeInstanceOf(CollectAction::class);
    expect($action->label)->toBe('test action');
    expect($action->description)->toBe('test description');
    expect($action->destination)->toBeInstanceOf(CollectActionDestinationHttp::class);
    expect($action->destination->url)->toBe('https://vleap.ai');
    expect($action->destination->method)->toBe('POST');
    expect($action->destination->headers)->toBe(['Authorization' => 'Bearer token']);
    expect($action->next)->toBe('next-action');
});

it('creates a collect action from array with string destination', function () {
    $data = [
        'label' => 'test action',
        'destination' => 'https://example.com',
        'inputs' => [],
    ];

    $action = CollectActionTransformer::fromArray($data);

    expect($action)->toBeInstanceOf(CollectAction::class);
    expect($action->label)->toBe('test action');
    expect($action->destination)->toBe('https://example.com');
});

it('creates a collect action from array with null or missing destination', function () {
    $data = [
        'label' => 'test action',
        'inputs' => [],
    ];

    $action = CollectActionTransformer::fromArray($data);

    expect($action)->toBeInstanceOf(CollectAction::class);
    expect($action->label)->toBe('test action');
    expect($action->destination)->toBeNull();
});

it('throws exception when creating collect action without label', function () {
    $data = [
        'destination' => 'https://example.com',
        'inputs' => [],
    ];

    expect(fn() => CollectActionTransformer::fromArray($data))
        ->toThrow(InvalidArgumentException::class, 'collect action label is required');
});

it('throws exception when creating collect action with destination object without url', function () {
    $data = [
        'label' => 'test action',
        'destination' => [
            'method' => 'POST',
        ],
        'inputs' => [],
    ];

    expect(fn() => CollectActionTransformer::fromArray($data))
        ->toThrow(InvalidArgumentException::class, 'collect action destination url is required');
});

it('round-trips collect action', function () {
    $original = new CollectAction(
        label: 'test action',
        description: 'test description',
        destination: new CollectActionDestinationHttp('https://vleap.ai', 'POST', ['Authorization' => 'Bearer token']),
        inputs: collect(),
        next: 'next-action',
    );

    $array = CollectActionTransformer::toArray($original);
    $restored = CollectActionTransformer::fromArray($array);

    expect($restored->label)->toBe($original->label);
    expect($restored->description)->toBe($original->description);
    expect($restored->destination)->toBeInstanceOf(CollectActionDestinationHttp::class);
    expect($restored->destination->url)->toBe($original->destination->url);
    expect($restored->destination->method)->toBe($original->destination->method);
    expect($restored->destination->headers)->toBe($original->destination->headers);
    expect($restored->next)->toBe($original->next);
});
