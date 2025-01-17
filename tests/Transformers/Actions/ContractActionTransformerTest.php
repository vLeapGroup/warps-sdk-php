<?php

use Brick\Math\BigInteger;
use Vleap\Warps\WarpAction;
use MultiversX\Address;
use Vleap\Warps\Actions\ActionType;
use Vleap\Warps\Transformers\Actions\ContractActionTransformer;

it('transforms a contract action', function () {
    $address = Address::zero();
    $action = WarpAction::create('test action')->contract($address, 'test endpoint', ['test arg'], BigInteger::zero(), 10000000);

    $actual = ContractActionTransformer::toArray($action);

    expect($actual)->toBe([
        'type' => ActionType::Contract->value,
        'label' => 'test action',
        'description' => null,
        'address' => 'erd1qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq6gq4hu',
        'func' => 'test endpoint',
        'args' => ['test arg'],
        'value' => '0',
        'gasLimit' => '10000000',
    ]);
});
