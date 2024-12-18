<?php

use Vleap\Warp;
use Vleap\WarpBuilder;

it('createFromRaw', function () {
    $warpRaw = json_decode(file_get_contents(__DIR__.'/examples/create-token.json'), true);

    $warp = WarpBuilder::createFromRaw($warpRaw);

    expect($warp)->toBeInstanceOf(Warp::class);
});
