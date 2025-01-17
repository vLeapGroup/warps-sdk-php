<?php

use Vleap\Warps\Warp;
use Vleap\Warps\WarpAction;
use Vleap\Warps\WarpBuilder;
use Vleap\Warps\Transformers\WarpTransformer;
use Vleap\Warps\Transformers\Actions\ActionTransformer;

it('transforms a warp', function () {
    $warp = (new WarpBuilder)
        ->setProtocol('warp:0.1.0')
        ->setName('test name')
        ->setTitle('test title')
        ->setDescription('test description')
        ->setPreview('https://abc.com/preview.png')
        ->addAction($action = WarpAction::create('test action')->link('https://example.com'))
        ->build();

    $actual = WarpTransformer::toArray($warp);

    expect($actual)->toBe([
        'protocol' => 'warp:0.1.0',
        'name' => 'test name',
        'title' => 'test title',
        'description' => 'test description',
        'preview' => 'https://abc.com/preview.png',
        'actions' => [
            ActionTransformer::toArray($action),
        ],
    ]);
});
