<?php

use Vleap\WarpAction;
use MultiversX\Address;
use Vleap\Actions\ActionType;

it('transforms a contract action', function () {
    $address = Address::zero();
    $action = WarpAction::create('test action')->contract($address, 'test endpoint', ['test arg'], 10000000)
        ->toArray();

    expect($action)->toBe([
        'type' => ActionType::Contract->value,
        'name' => 'test action',
        'description' => null,
        'address' => '0000000000000000000000000000000000000000000000000000000000000000',
        'endpoint' => 'test endpoint',
        'args' => ['test arg'],
        'gasLimit' => '10000000',
    ]);
});
