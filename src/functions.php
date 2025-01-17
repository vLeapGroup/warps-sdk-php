<?php

namespace Vleap\Warps;

use MultiversX\Address;
use Brick\Math\BigInteger;
use MultiversX\TokenTransfer;
use MultiversX\SmartContracts\CodeMetadata;
use MultiversX\SmartContracts\Typesystem\Field;
use MultiversX\SmartContracts\Typesystem\Struct;
use MultiversX\SmartContracts\Typesystem\U8Value;
use MultiversX\SmartContracts\Typesystem\U16Value;
use MultiversX\SmartContracts\Typesystem\U32Value;
use MultiversX\SmartContracts\Typesystem\U64Value;
use MultiversX\SmartContracts\Typesystem\ListValue;
use MultiversX\SmartContracts\Typesystem\BytesValue;
use MultiversX\SmartContracts\Typesystem\TypedValue;
use MultiversX\SmartContracts\Typesystem\OptionValue;
use MultiversX\SmartContracts\Typesystem\StringValue;
use MultiversX\SmartContracts\Typesystem\AddressValue;
use MultiversX\SmartContracts\Typesystem\BigUIntValue;
use MultiversX\SmartContracts\Typesystem\BooleanValue;
use MultiversX\SmartContracts\Typesystem\NothingValue;
use MultiversX\SmartContracts\Typesystem\OptionalValue;
use MultiversX\SmartContracts\Typesystem\Types\U64Type;
use MultiversX\SmartContracts\Typesystem\VariadicValue;
use MultiversX\SmartContracts\Typesystem\CompositeValue;
use MultiversX\SmartContracts\Typesystem\Types\ListType;
use MultiversX\SmartContracts\Typesystem\Types\StructType;
use MultiversX\SmartContracts\Typesystem\CodeMetadataValue;
use MultiversX\SmartContracts\Typesystem\Types\BigUIntType;
use MultiversX\SmartContracts\Typesystem\Types\OptionalType;
use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\SmartContracts\Typesystem\TokenIdentifierValue;
use MultiversX\SmartContracts\Typesystem\Types\FieldDefinition;
use MultiversX\SmartContracts\Typesystem\Types\TokenIdentifierType;

function option_of(?TypedValue $value): OptionValue
{
    return $value ? OptionValue::newProvided($value) : OptionValue::newMissing();
}

function optional_pf(?TypedValue $value): OptionalValue
{
    return $value ? new OptionalValue(new OptionalType($value->getType()), $value) : OptionalValue::newMissing();
}

function list_of(array $values): ListValue
{
    if (empty($values)) {
        throw new \Exception('Cannot create a list from an empty array');
    }
    $type = $values[0]->getType();
    return new ListValue(new ListType($type), $values);
}

function variadic_of(array $values): VariadicValue
{
    return VariadicValue::fromItems(...$values);
}

function composite_of(array $values): CompositeValue
{
    $types = array_map(fn($value) => $value->getType(), $values);
    return new CompositeValue(new CompositeType(...$types), $values);
}

function string(string $value): StringValue
{
    return StringValue::fromUTF8($value);
}

function u8(int $value): U8Value
{
    return new U8Value($value);
}

function u16(int $value): U16Value
{
    return new U16Value($value);
}

function u32(int $value): U32Value
{
    return new U32Value($value);
}

function u64(int|string $value): U64Value
{
    return new U64Value($value);
}

function biguint(int|string|BigInteger $value): BigUIntValue
{
    return new BigUIntValue($value);
}

function boolean(bool $value): BooleanValue
{
    return new BooleanValue($value);
}

function address(string $value): AddressValue
{
    return new AddressValue(Address::newFromBech32($value));
}

function token(string $value): TokenIdentifierValue
{
    return new TokenIdentifierValue($value);
}

function hex(string $value): BytesValue
{
    return BytesValue::fromHex($value);
}

function esdt(TokenTransfer $value): Struct
{
    return new Struct(
        new StructType('EsdtTokenPayment', [
            new FieldDefinition('token_identifier', '', new TokenIdentifierType()),
            new FieldDefinition('token_nonce', '', new U64Type()),
            new FieldDefinition('amount', '', new BigUIntType()),
        ]),
        [
            new Field(new TokenIdentifierValue($value->token->identifier), 'token_identifier'),
            new Field(new U64Value($value->token->nonce), 'token_nonce'),
            new Field(new BigUIntValue($value->amount), 'amount'),
        ]
    );
}

function codemeta(string $hexString): CodeMetadataValue
{
    return new CodeMetadataValue(CodeMetadata::fromBuffer(hex2bin($hexString)));
}

function nothing(): NothingValue
{
    return new NothingValue();
}
