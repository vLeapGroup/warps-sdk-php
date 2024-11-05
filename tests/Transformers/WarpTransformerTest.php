<?php

use Vleap\Warp;
use Vleap\WarpAction;
use Vleap\WarpBuilder;

it('transforms a warp', function () {
    $actual = (new WarpBuilder('test name'))
        ->setTitle('test title')
        ->setDescription('test description')
        ->setPreview('https://abc.com/preview.png')
        ->addAction($action = WarpAction::create('test action')->link('https://example.com'))
        ->build()
        ->toArray();

    expect($actual)->toBe([
        'name' => 'test name',
        'title' => 'test title',
        'description' => 'test description',
        'preview' => 'https://abc.com/preview.png',
        'actions' => [
            $action->toArray(),
        ],
    ]);
});
