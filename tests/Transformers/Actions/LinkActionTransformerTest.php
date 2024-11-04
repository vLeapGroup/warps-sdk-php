<?php

use Vleap\WarpAction;

it('transforms a link action', function () {
    $action = WarpAction::create('test action')->link('https://example.com')
        ->toArray();

    expect($action)->toBe([
        'name' => 'test action',
        'description' => null,
        'type' => 'link',
        'url' => 'https://example.com',
    ]);
});
