<?php

use Vleap\Warp;
use Vleap\WarpAction;
use Vleap\WarpBuilder;

it('creates a basic warp', function () {
    $actual = (new WarpBuilder('test name'))
        ->setTitle('test title')
        ->setDescription('test description')
        ->setPreview('https://abc.com/preview.png')
        ->build();

    expect($actual)->toBeInstanceOf(Warp::class);
});

it('adds an action to the warp', function () {
    $actual = (new WarpBuilder('test name'))
        ->setTitle('test title')
        ->setDescription('test description')
        ->setPreview('https://abc.com/preview.png')
        ->addAction($action = WarpAction::create('test action')->link('https://example.com'))
        ->build();

    expect($actual)->toBeInstanceOf(Warp::class);
    expect($actual->actions->count())->toBe(1);
    expect($actual->actions->first())->toBe($action);
});
