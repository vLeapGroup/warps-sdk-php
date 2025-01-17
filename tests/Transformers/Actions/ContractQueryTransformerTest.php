<?php

use Brick\Math\BigInteger;
use Vleap\Warps\WarpAction;
use MultiversX\Address;
use Vleap\Warps\Actions\ActionType;
use Vleap\Warps\Transformers\Actions\QueryActionTransformer;

it('transforms a contract query', function () {
    $address = Address::zero();
    $action = WarpAction::create('test action')->query($address, 'test endpoint', ['test arg'], 'https://vleap.io/abi.json');

    $actual = QueryActionTransformer::toArray($action);

    expect($actual)->toBe([
        'type' => ActionType::Query->value,
        'label' => 'test action',
        'description' => null,
        'address' => 'erd1qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq6gq4hu',
        'func' => 'test endpoint',
        'args' => ['test arg'],
        'abi' => 'https://vleap.io/abi.json',
    ]);
});
