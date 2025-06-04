<?php

use Vleap\Warps\WarpAction;
use Vleap\Warps\Actions\ActionType;
use Vleap\Warps\Actions\CollectActionDestination;
use Vleap\Warps\Transformers\Actions\CollectActionTransformer;

it('transforms a collect action', function () {
    $action = WarpAction::create('test action')
        ->collect(new CollectActionDestination('https://vleap.ai', 'POST', ['Authorization' => 'Bearer token']), collect());

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
