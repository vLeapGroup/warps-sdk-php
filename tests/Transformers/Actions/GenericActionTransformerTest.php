<?php

use Vleap\Warps\Actions\ActionType;
use Vleap\Warps\Actions\GenericAction;
use Vleap\Warps\Transformers\Actions\GenericActionTransformer;
use Vleap\Warps\Transformers\Actions\ActionTransformer;

it('creates a state action from array', function () {
    $data = [
        'type' => 'state',
        'label' => 'Save secret',
        'op' => 'write',
        'store' => 'guessing-game',
        'data' => ['secret' => '{{secret}}', 'active' => true],
    ];

    $action = GenericActionTransformer::fromArray($data);

    expect($action)->toBeInstanceOf(GenericAction::class);
    expect($action->getType())->toBe(ActionType::State);
    expect($action->getData())->toBe($data);
});

it('creates a mount action from array', function () {
    $data = ['type' => 'mount', 'label' => 'Mount guess listener', 'warp' => '@game-guess-number-check'];

    $action = GenericActionTransformer::fromArray($data);

    expect($action)->toBeInstanceOf(GenericAction::class);
    expect($action->getType())->toBe(ActionType::Mount);
});

it('creates an unmount action from array', function () {
    $data = ['type' => 'unmount', 'label' => 'Unmount', 'warp' => '@game-guess-number-check'];

    $action = GenericActionTransformer::fromArray($data);

    expect($action)->toBeInstanceOf(GenericAction::class);
    expect($action->getType())->toBe(ActionType::Unmount);
});

it('round-trips a state action without data loss', function () {
    $data = [
        'type' => 'state',
        'label' => 'Read game state',
        'op' => 'read',
        'store' => 'guessing-game',
        'keys' => ['secret', 'active'],
    ];

    $action = GenericActionTransformer::fromArray($data);
    $restored = GenericActionTransformer::toArray($action);

    expect($restored)->toBe($data);
});

it('ActionTransformer dispatches state/mount/unmount to GenericAction', function () {
    foreach (['state', 'mount', 'unmount'] as $type) {
        $action = ActionTransformer::fromArray(['type' => $type, 'label' => 'test']);

        expect($action)->toBeInstanceOf(GenericAction::class);
        expect($action->getType()->value)->toBe($type);
    }
});
