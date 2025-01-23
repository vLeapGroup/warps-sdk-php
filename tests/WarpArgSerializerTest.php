<?php

use Brick\Math\BigInteger;
use MultiversX\Token;
use MultiversX\Address;
use MultiversX\TokenTransfer;
use MultiversX\SmartContracts\CodeMetadata;
use MultiversX\SmartContracts\Typesystem\{
    U8Value, U16Value, U32Value, U64Value, BigUIntValue,
    StringValue, BooleanValue, AddressValue, BytesValue,
    TokenIdentifierValue, CodeMetadataValue, ListValue,
    VariadicValue, CompositeValue, OptionValue, OptionalValue,
    Struct, Field
};
use MultiversX\SmartContracts\Typesystem\Types\{
    StringType, U64Type, CompositeType, ListType, OptionalType, OptionType, VariadicType
};
use Vleap\Warps\WarpArgSerializer;

use function Vleap\Warps\esdt;

beforeEach(function () {
    $this->serializer = new WarpArgSerializer();
});

describe('nativeToString', function () {
    it('serializes address values', function () {
        $result = $this->serializer->nativeToString('address', 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l');
        expect($result)->toBe('address:erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l');
    });

    it('serializes bool values', function () {
        expect($this->serializer->nativeToString('bool', true))->toBe('bool:true');
        expect($this->serializer->nativeToString('bool', false))->toBe('bool:false');
    });

    it('serializes biguint values', function () {
        $bigValue = BigInteger::of('1234567890');
        expect($this->serializer->nativeToString('biguint', $bigValue))->toBe('biguint:1234567890');
    });

    it('serializes uint values', function () {
        expect($this->serializer->nativeToString('uint64', 123))->toBe('uint64:123');
        expect($this->serializer->nativeToString('uint32', 456))->toBe('uint32:456');
        expect($this->serializer->nativeToString('uint16', 789))->toBe('uint16:789');
        expect($this->serializer->nativeToString('uint8', 255))->toBe('uint8:255');
    });

    it('serializes string values', function () {
        expect($this->serializer->nativeToString('string', 'hello'))->toBe('string:hello');
    });

    it('serializes hex values', function () {
        expect($this->serializer->nativeToString('hex', '0x1234'))->toBe('hex:0x1234');
    });

    it('serializes esdt values', function () {
        $token = new Token(identifier: 'AAA-123456', nonce: BigInteger::of(5));
        $transfer = new TokenTransfer(token: $token, amount: BigInteger::of(100));
        expect($this->serializer->nativeToString('esdt', $transfer))->toBe('esdt:AAA-123456|5|100');
    });
});

describe('typedToString', function () {
    it('converts OptionValue to native value', function () {
        $result = $this->serializer->typedToString(new OptionValue(new OptionType(new StringType()), StringValue::fromUTF8('abc')));
        expect($result)->toBe('option:string:abc');
    });

    it('converts OptionalValue to native value', function () {
        $result = $this->serializer->typedToString(new OptionalValue(new OptionalType(new StringType()), StringValue::fromUTF8('abc')));
        expect($result)->toBe('optional:string:abc');
    });

    it('converts ListValue to native value', function () {
        $result = $this->serializer->typedToString(new ListValue(new ListType(new StringType()), [
            StringValue::fromUTF8('abc'),
            StringValue::fromUTF8('def')
        ]));
        expect($result)->toBe('list:string:abc,def');
    });

    it('converts VariadicValue to native value', function () {
        $result = $this->serializer->typedToString(
            new VariadicValue(new VariadicType(new StringType()), [
                StringValue::fromUTF8('abc'),
                StringValue::fromUTF8('def')
            ])
        );
        expect($result)->toBe('variadic:string:abc,def');
    });

    it('converts CompositeValue to native value', function () {
        $result = $this->serializer->typedToString(
            new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                StringValue::fromUTF8('abc'),
                new U64Value('12345678901234567890'),
            ])
        );
        expect($result)->toBe('composite(string|uint64):abc|12345678901234567890');
    });

    it('converts BigUIntValue to biguint', function () {
        $result = $this->serializer->typedToString(new BigUIntValue(BigInteger::of('123456789012345678901234567890')));
        expect($result)->toBe('biguint:123456789012345678901234567890');
    });

    it('converts U8Value to uint8', function() {
        $result = $this->serializer->typedToString(new U8Value(255));
        expect($result)->toBe('uint8:255');
    });

    it('converts U16Value to uint16', function() {
        $result = $this->serializer->typedToString(new U16Value(65535));
        expect($result)->toBe('uint16:65535');
    });

    it('converts U32Value to uint32', function() {
        $result = $this->serializer->typedToString(new U32Value(4294967295));
        expect($result)->toBe('uint32:4294967295');
    });

    it('converts U64Value to uint64', function() {
        $result = $this->serializer->typedToString(new U64Value(BigInteger::of('18446744073709551615')));
        expect($result)->toBe('uint64:18446744073709551615');
    });

    it('converts StringValue to string', function() {
        $result = $this->serializer->typedToString(StringValue::fromUTF8('hello'));
        expect($result)->toBe('string:hello');
    });

    it('converts BooleanValue to bool', function() {
        $result = $this->serializer->typedToString(new BooleanValue(true));
        expect($result)->toBe('bool:true');
    });

    it('converts AddressValue to address', function() {
        $address = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
        $result = $this->serializer->typedToString(new AddressValue(Address::newFromBech32($address)));
        expect($result)->toBe("address:{$address}");
    });

    it('converts TokenIdentifierValue to token', function() {
        $result = $this->serializer->typedToString(new TokenIdentifierValue('1234'));
        expect($result)->toBe('token:1234');
    });

    it('converts BytesValue to hex', function() {
        $result = $this->serializer->typedToString(BytesValue::fromHex('1234'));
        expect($result)->toBe('hex:1234');
    });

    it('converts CodeMetadataValue to codemeta', function() {
        $result = $this->serializer->typedToString(new CodeMetadataValue(new CodeMetadata(true, false, true, true)));
        expect($result)->toBe('codemeta:0106');
    });

    it('converts EsdtTokenPayment Struct to esdt', function() {
        $token = new Token(identifier: 'AAA-123456', nonce: BigInteger::of(5));
        $transfer = new TokenTransfer(token: $token, amount: BigInteger::of(100));
        $result = $this->serializer->typedToString(esdt($transfer));
        expect($result)->toBe('esdt:AAA-123456|5|100');
    });

    it('converts a composite value', function() {
        $result = $this->serializer->typedToString(
            new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                StringValue::fromUTF8('abc'),
                new U64Value(BigInteger::of(123))
            ])
        );
        expect($result)->toBe('composite(string|uint64):abc|123');
    });

    it('converts nested List of CompositeValue', function() {
        $result = $this->serializer->typedToString(
            new ListValue(new ListType(new CompositeType(new StringType(), new U64Type())), [
                new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                    StringValue::fromUTF8('abc'),
                    new U64Value(BigInteger::of(123))
                ]),
                new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                    StringValue::fromUTF8('def'),
                    new U64Value(BigInteger::of(456))
                ]),
                new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                    StringValue::fromUTF8('ghi'),
                    new U64Value(BigInteger::of(789))
                ])
            ])
        );
        expect($result)->toBe('list:composite(string|uint64):abc|123,def|456,ghi|789');
    });

    it('converts nested VariadicValue of CompositeValue', function() {
        $result = $this->serializer->typedToString(
            new VariadicValue(new VariadicType(new CompositeType(new StringType(), new U64Type())), [
                new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                    StringValue::fromUTF8('abc'),
                    new U64Value(BigInteger::of(123))
                ]),
                new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                    StringValue::fromUTF8('def'),
                    new U64Value(BigInteger::of(456))
                ]),
                new CompositeValue(new CompositeType(new StringType(), new U64Type()), [
                    StringValue::fromUTF8('ghi'),
                    new U64Value(BigInteger::of(789))
                ])
            ])
        );
        expect($result)->toBe('variadic:composite(string|uint64):abc|123,def|456,ghi|789');
    });
});

describe('nativeToTyped', function () {
    it('converts option to OptionValue', function () {
        $result = $this->serializer->nativeToTyped('option:string', 'hello');
        expect($result)->toBeInstanceOf(OptionValue::class);
        expect($result->valueOf())->toBe('hello');
    });

    it('converts option to OptionValue with missing value', function () {
        $result = $this->serializer->nativeToTyped('option:string', null);
        expect($result)->toBeInstanceOf(OptionValue::class);
        expect($result->valueOf())->toBeNull();
    });

    it('converts optional to OptionalValue', function () {
        $result = $this->serializer->nativeToTyped('optional:string', 'hello');
        expect($result)->toBeInstanceOf(OptionalValue::class);
        expect($result->valueOf())->toBe('hello');
    });

    it('converts optional to OptionalValue with missing value', function () {
        $result = $this->serializer->nativeToTyped('optional:string', null);
        expect($result)->toBeInstanceOf(OptionalValue::class);
        expect($result->valueOf())->toBeNull();
    });

    it('converts list to ListValue', function () {
        $result = $this->serializer->nativeToTyped('list:string', 'hello,world');
        expect($result)->toBeInstanceOf(ListValue::class);
        $items = $result->getItems();
        expect($items[0])->toBeInstanceOf(StringValue::class);
        expect($items[0]->valueOf())->toBe('hello');
        expect($items[1])->toBeInstanceOf(StringValue::class);
        expect($items[1]->valueOf())->toBe('world');
    });

    it('converts variadic to VariadicValue', function () {
        $result = $this->serializer->nativeToTyped('variadic:string', 'hello,world');
        expect($result)->toBeInstanceOf(VariadicValue::class);
        $items = $result->getItems();
        expect($items[0])->toBeInstanceOf(StringValue::class);
        expect($items[0]->valueOf())->toBe('hello');
        expect($items[1])->toBeInstanceOf(StringValue::class);
        expect($items[1]->valueOf())->toBe('world');
    });

    it('converts composite to CompositeValue', function () {
        $result = $this->serializer->nativeToTyped('composite(string|uint64|uint8)', 'hello|12345678901234567890|255');
        expect($result)->toBeInstanceOf(CompositeValue::class);
        $items = $result->getItems();
        expect($items[0])->toBeInstanceOf(StringValue::class);
        expect($items[0]->valueOf())->toBe('hello');
        expect($items[1])->toBeInstanceOf(U64Value::class);
        expect((string) $items[1]->valueOf())->toBe('12345678901234567890');
        expect($items[2])->toBeInstanceOf(U8Value::class);
        expect((string) $items[2]->valueOf())->toBe('255');
    });

    it('converts string to StringValue', function() {
        $result = $this->serializer->nativeToTyped('string', 'hello');
        expect($result)->toBeInstanceOf(StringValue::class);
        expect($result->valueOf())->toBe('hello');
    });

    it('converts uint8 to U8Value', function() {
        $result = $this->serializer->nativeToTyped('uint8', 255);
        expect($result)->toBeInstanceOf(U8Value::class);
        expect((string) $result->valueOf())->toBe('255');
    });

    it('converts uint16 to U16Value', function() {
        $result = $this->serializer->nativeToTyped('uint16', 65535);
        expect($result)->toBeInstanceOf(U16Value::class);
        expect((string) $result->valueOf())->toBe('65535');
    });

    it('converts uint32 to U32Value', function() {
        $result = $this->serializer->nativeToTyped('uint32', 4294967295);
        expect($result)->toBeInstanceOf(U32Value::class);
        expect((string) $result->valueOf())->toBe('4294967295');
    });

    it('converts uint64 to U64Value', function() {
        $result = $this->serializer->nativeToTyped('uint64', '18446744073709551615');
        expect($result)->toBeInstanceOf(U64Value::class);
        expect((string) $result->valueOf())->toBe('18446744073709551615');
    });

    it('converts biguint to BigUIntValue', function() {
        $result = $this->serializer->nativeToTyped('biguint', '123456789012345678901234567890');
        expect($result)->toBeInstanceOf(BigUIntValue::class);
        expect((string) $result->valueOf())->toBe('123456789012345678901234567890');
    });

    it('converts bool to BooleanValue', function() {
        $result = $this->serializer->nativeToTyped('bool', true);
        expect($result)->toBeInstanceOf(BooleanValue::class);
        expect($result->valueOf())->toBeTrue();
    });

    it('converts address to AddressValue', function() {
        $address = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
        $result = $this->serializer->nativeToTyped('address', $address);
        expect($result)->toBeInstanceOf(AddressValue::class);
        expect($result->valueOf())->toBeInstanceOf(Address::class);
        expect($result->valueOf()->bech32())->toBe($address);
    });

    it('converts token to TokenIdentifierValue', function() {
        $result = $this->serializer->nativeToTyped('token', '1234');
        expect($result)->toBeInstanceOf(TokenIdentifierValue::class);
        expect($result->valueOf())->toBe('1234');
    });

    it('converts hex to BytesValue', function() {
        $result = $this->serializer->nativeToTyped('hex', '1234');
        expect($result)->toBeInstanceOf(BytesValue::class);
        $hexValue = bin2hex($result->valueOf());
        expect($hexValue)->toBe('1234');
    });

    it('converts codemeta to CodeMetadataValue', function() {
        $result = $this->serializer->nativeToTyped('codemeta', '0106');
        expect($result)->toBeInstanceOf(CodeMetadataValue::class);
        expect($result->valueOf())->toBeInstanceOf(CodeMetadata::class);
        expect(bin2hex($result->valueOf()->toBuffer()))->toBe('0106');
    });

    it('converts esdt to EsdtTokenPayment Struct', function() {
        $token = new Token(identifier: 'AAA-123456', nonce: BigInteger::of(5));
        $transfer = new TokenTransfer(token: $token, amount: BigInteger::of(100));
        $result = $this->serializer->nativeToTyped('esdt', $transfer);
        expect($result)->toBeInstanceOf(Struct::class);
        expect($result->getFieldValue('token_identifier'))->toBe('AAA-123456');
        expect((string) $result->getFieldValue('token_nonce'))->toBe('5');
        expect((string) $result->getFieldValue('amount'))->toBe('100');
    });

    it('throws error for unsupported type', function() {
        expect(fn() => $this->serializer->nativeToTyped('unsupported', 'value'))
            ->toThrow(Exception::class, 'Unsupported input type');
    });
});

describe('stringToNative', function () {
    it('deserializes option', function () {
        $result = $this->serializer->stringToNative('option:string:hello');
        expect($result[0])->toBe('option:string');
        expect($result[1])->toBe('hello');
    });

    it('deserializes option with missing value', function () {
        $result = $this->serializer->stringToNative('option:string');
        expect($result[0])->toBe('option:string');
        expect($result[1])->toBeNull();
    });

    it('deserializes optional', function () {
        $result = $this->serializer->stringToNative('optional:string:hello');
        expect($result[0])->toBe('optional:string');
        expect($result[1])->toBe('hello');
    });

    it('deserializes optional with missing value', function () {
        $result = $this->serializer->stringToNative('optional:string');
        expect($result[0])->toBe('optional:string');
        expect($result[1])->toBeNull();
    });

    it('deserializes a simple list', function () {
        $result = $this->serializer->stringToNative('list:string:hello,world');
        expect($result[0])->toBe('list:string');
        expect($result[1])->toBe(['hello', 'world']);
    });

    it('deserializes an empty list', function () {
        $result = $this->serializer->stringToNative('list:string:');
        expect($result[0])->toBe('list:string');
        expect($result[1])->toBe([]);
    });

    it('deserializes a list of composite values', function () {
        $result = $this->serializer->stringToNative('list:composite(string|uint64):hello|123,world|456');
        expect($result[0])->toBe('list:composite(string|uint64)');
        $values = $result[1];
        expect($values[0][0])->toBe('hello');
        expect((string) $values[0][1])->toBe('123');
        expect($values[1][0])->toBe('world');
        expect((string) $values[1][1])->toBe('456');
    });

    it('deserializes a list of empty values', function () {
        $result = $this->serializer->stringToNative('list:composite(string|uint64):');
        expect($result[0])->toBe('list:composite(string|uint64)');
        expect($result[1])->toBe([]);
    });

    it('deserializes variadic of u64', function () {
        $result = $this->serializer->stringToNative('variadic:uint64:123,456,789');
        expect($result[0])->toBe('variadic:uint64');
        $values = $result[1];
        expect((string) $values[0])->toBe('123');
        expect((string) $values[1])->toBe('456');
        expect((string) $values[2])->toBe('789');
    });

    it('deserializes variadic of composite', function () {
        $result = $this->serializer->stringToNative('variadic:composite(string|uint64):abc|123,def|456,ghi|789');
        expect($result[0])->toBe('variadic:composite(string|uint64)');
        $values = $result[1];
        expect($values[0][0])->toBe('abc');
        expect((string) $values[0][1])->toBe('123');
        expect($values[1][0])->toBe('def');
        expect((string) $values[1][1])->toBe('456');
        expect($values[2][0])->toBe('ghi');
        expect((string) $values[2][1])->toBe('789');
    });

    it('deserializes variadic of empty values', function () {
        $result = $this->serializer->stringToNative('variadic:string:');
        expect($result[0])->toBe('variadic:string');
        expect($result[1])->toBe([]);
    });

    it('deserializes composite values', function () {
        $result = $this->serializer->stringToNative('composite(string|uint64):hello|123');
        expect($result[0])->toBe('composite(string|uint64)');
        $values = $result[1];
        expect($values[0])->toBe('hello');
        expect((string) $values[1])->toBe('123');
    });

    it('deserializes string values', function () {
        expect($this->serializer->stringToNative('string:hello'))->toBe(['string', 'hello']);
    });

    it('deserializes uint values', function () {
        expect($this->serializer->stringToNative('uint8:255'))->toBe(['uint8', 255]);
        expect($this->serializer->stringToNative('uint16:789'))->toBe(['uint16', 789]);
        expect($this->serializer->stringToNative('uint32:456'))->toBe(['uint32', 456]);
    });

    it('deserializes uint64 values', function () {
        $result = $this->serializer->stringToNative('uint64:1234567890');
        expect($result[0])->toBe('uint64');
        expect((string) $result[1])->toBe('1234567890');
    });

    it('deserializes biguint values', function () {
        $result = $this->serializer->stringToNative('biguint:1234567890');
        expect($result[0])->toBe('biguint');
        expect((string) $result[1])->toBe('1234567890');
    });

    it('deserializes bool values', function () {
        expect($this->serializer->stringToNative('bool:true'))->toBe(['bool', true]);
        expect($this->serializer->stringToNative('bool:false'))->toBe(['bool', false]);
    });

    it('deserializes address values', function () {
        $address = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
        $result = $this->serializer->stringToNative("address:{$address}");
        expect($result)->toBe(['address', $address]);
    });

    it('deserializes token values', function () {
        $result = $this->serializer->stringToNative('token:TOKEN-123456');
        expect($result)->toBe(['token', 'TOKEN-123456']);
    });

    it('deserializes hex values', function () {
        expect($this->serializer->stringToNative('hex:0x1234'))->toBe(['hex', '0x1234']);
    });

    it('deserializes codemeta values', function () {
        expect($this->serializer->stringToNative('codemeta:0106'))->toBe(['codemeta', '0106']);
    });

    it('deserializes esdt values', function () {
        $result = $this->serializer->stringToNative('esdt:AAA-123456|5|100');
        expect($result[0])->toBe('esdt');
        $transfer = $result[1];
        expect($transfer)->toBeInstanceOf(TokenTransfer::class);
        expect($transfer->token->identifier)->toBe('AAA-123456');
        expect((string) $transfer->token->nonce)->toBe('5');
        expect((string) $transfer->amount)->toBe('100');
    });
});

describe('stringToTyped', function () {
    it('converts string encoded value to StringValue', function () {
        $result = $this->serializer->stringToTyped('string:hello');
        expect($result)->toBeInstanceOf(StringValue::class);
        expect($result->valueOf())->toBe('hello');
    });

    it('converts uint encoded values to respective UValue types', function () {
        $uint8Result = $this->serializer->stringToTyped('uint8:255');
        expect($uint8Result)->toBeInstanceOf(U8Value::class);
        expect((string) $uint8Result)->toBe('255');

        $uint16Result = $this->serializer->stringToTyped('uint16:65535');
        expect($uint16Result)->toBeInstanceOf(U16Value::class);
        expect((string) $uint16Result)->toBe('65535');

        $uint32Result = $this->serializer->stringToTyped('uint32:4294967295');
        expect($uint32Result)->toBeInstanceOf(U32Value::class);
        expect((string) $uint32Result)->toBe('4294967295');

        $uint64Result = $this->serializer->stringToTyped('uint64:18446744073709551615');
        expect($uint64Result)->toBeInstanceOf(U64Value::class);
        expect((string) $uint64Result)->toBe('18446744073709551615');
    });

    it('converts biguint encoded value to BigUIntValue', function () {
        $result = $this->serializer->stringToTyped('biguint:123456789012345678901234567890');
        expect($result)->toBeInstanceOf(BigUIntValue::class);
        expect((string) $result->valueOf())->toBe('123456789012345678901234567890');
    });

    it('converts bool encoded value to BooleanValue', function () {
        $trueResult = $this->serializer->stringToTyped('bool:true');
        expect($trueResult)->toBeInstanceOf(BooleanValue::class);
        expect($trueResult->valueOf())->toBeTrue();

        $falseResult = $this->serializer->stringToTyped('bool:false');
        expect($falseResult)->toBeInstanceOf(BooleanValue::class);
        expect($falseResult->valueOf())->toBeFalse();
    });

    it('converts address encoded value to AddressValue', function () {
        $address = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
        $result = $this->serializer->stringToTyped("address:{$address}");
        expect($result)->toBeInstanceOf(AddressValue::class);
        expect($result->valueOf())->toBeInstanceOf(Address::class);
        expect($result->valueOf()->bech32())->toBe($address);
    });

    it('converts hex encoded value to BytesValue', function () {
        $result = $this->serializer->stringToTyped('hex:1234');
        expect($result)->toBeInstanceOf(BytesValue::class);
        $hexValue = bin2hex($result->valueOf());
        expect($hexValue)->toBe('1234');
    });

    it('converts nested variadic of composite', function () {
        $result = $this->serializer->stringToTyped('variadic:composite(string|uint64):abc|123,def|456,ghi|789');
        expect($result)->toBeInstanceOf(VariadicValue::class);
        $values = $result->getItems();

        $actualFirst = $values[0]->getItems();
        expect((string) $actualFirst[0]->valueOf())->toBe('abc');
        expect((string) $actualFirst[1]->valueOf())->toBe('123');

        $actualSecond = $values[1]->getItems();
        expect((string) $actualSecond[0]->valueOf())->toBe('def');
        expect((string) $actualSecond[1]->valueOf())->toBe('456');

        $actualThird = $values[2]->getItems();
        expect((string) $actualThird[0]->valueOf())->toBe('ghi');
        expect((string) $actualThird[1]->valueOf())->toBe('789');
    });

    it('throws error for unsupported type', function () {
        expect(fn() => $this->serializer->stringToTyped('unsupported:value'))
            ->toThrow(Exception::class, 'Unsupported input type');
    });
});
