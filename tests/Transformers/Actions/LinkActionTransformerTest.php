<?php

use Vleap\Actions\ActionType;
use Vleap\WarpAction;

it('transforms a link action', function () {
    $action = WarpAction::create('test action')->link('https://example.com')
        ->toArray();

    expect($action)->toBe([
        'type' => ActionType::Link->value,
        'label' => 'test action',
        'description' => null,
        'url' => 'https://example.com',
    ]);
});
