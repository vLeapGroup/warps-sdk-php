<?php

use Vleap\WarpAction;
use Vleap\Actions\ActionType;
use Vleap\Transformers\Actions\LinkActionTransformer;

it('transforms a link action', function () {
    $action = WarpAction::create('test action')->link('https://example.com');

    $actual = LinkActionTransformer::toArray($action);

    expect($actual)->toBe([
        'type' => ActionType::Link->value,
        'label' => 'test action',
        'description' => null,
        'url' => 'https://example.com',
    ]);
});
