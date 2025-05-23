<?php

use Brick\Math\BigInteger;
use Vleap\Warps\WarpAction;
use MultiversX\Address;
use Vleap\Warps\Actions\ActionType;
use Vleap\Warps\Transformers\Actions\TransferActionTransformer;

it('transforms a transfer action', function () {
    $address = Address::zero();
    $action = WarpAction::create('test action')
        ->transfer($address, 'test data', BigInteger::of(1000000));

    $actual = TransferActionTransformer::toArray($action);

    expect($actual)->toBe([
        'type' => ActionType::Transfer->value,
        'label' => 'test action',
        'description' => null,
        'address' => 'erd1qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq6gq4hu',
        'data' => 'test data',
        'value' => '1000000',
    ]);
});
