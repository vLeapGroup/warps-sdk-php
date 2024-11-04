<?php

use Vleap\Warp;
use Vleap\WarpAction;

it('transforms a warp', function () {
    $warp = Warp::create('test name', 'test description');
    $action = WarpAction::create('test action')->link('https://example.com');

    $actual = $warp->addAction($action)
        ->toArray();

    expect($actual)->toBe([
        'name' => 'test name',
        'description' => 'test description',
        'actions' => [
            $action->toArray(),
        ],
    ]);
});
