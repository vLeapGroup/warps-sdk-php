<?php

use MultiversX\Address;
use Vleap\WarpAction;

it('transforms a contract action', function () {
    $address = Address::zero();
    $action = WarpAction::create('test action')->contract($address, 'test endpoint', ['test arg'], 10000000)
        ->toArray();

    expect($action)->toBe([
        'name' => 'test action',
        'description' => null,
        'type' => 'contract',
        'address' => '0000000000000000000000000000000000000000000000000000000000000000',
        'endpoint' => 'test endpoint',
        'args' => ['test arg'],
        'gasLimit' => '10000000',
    ]);
});
