<?php

use Vleap\Warp;
use Vleap\WarpAction;

it('creates a basic warp', function () {
    $actual = Warp::create('test name', 'test description');

    expect($actual)->toBeInstanceOf(Warp::class);
});

it('adds an action to the warp', function () {
    $warp = Warp::create('test name', 'test description');
    $action = new WarpAction('test action');

    $actual = $warp->addAction($action);

    expect($actual)->toBeInstanceOf(Warp::class);
    expect($actual->actions->count())->toBe(1);
    expect($actual->actions->first())->toBe($action);
});
