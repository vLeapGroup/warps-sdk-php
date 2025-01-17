<?php

use MultiversX\Token;
use MultiversX\Address;
use Brick\Math\BigInteger;
use MultiversX\TokenTransfer;
use Vleap\Warps\WarpArgSerializer;
use MultiversX\SmartContracts\CodeMetadata;
use MultiversX\SmartContracts\Typesystem\Field;
use MultiversX\SmartContracts\Typesystem\Struct;
use MultiversX\SmartContracts\Typesystem\U8Value;
use MultiversX\SmartContracts\Typesystem\U16Value;
use MultiversX\SmartContracts\Typesystem\U32Value;
use MultiversX\SmartContracts\Typesystem\U64Value;
use MultiversX\SmartContracts\Typesystem\ListValue;
use MultiversX\SmartContracts\Typesystem\BytesValue;
use MultiversX\SmartContracts\Typesystem\OptionValue;
use MultiversX\SmartContracts\Typesystem\StringValue;
use MultiversX\SmartContracts\Typesystem\AddressValue;
use MultiversX\SmartContracts\Typesystem\BigUIntValue;
use MultiversX\SmartContracts\Typesystem\BooleanValue;
use MultiversX\SmartContracts\Typesystem\OptionalValue;
use MultiversX\SmartContracts\Typesystem\Types\U64Type;
use MultiversX\SmartContracts\Typesystem\VariadicValue;
use MultiversX\SmartContracts\Typesystem\CompositeValue;
use MultiversX\SmartContracts\Typesystem\Types\ListType;
use MultiversX\SmartContracts\Typesystem\Types\OptionType;
use MultiversX\SmartContracts\Typesystem\Types\StringType;
use MultiversX\SmartContracts\Typesystem\Types\StructType;
use MultiversX\SmartContracts\Typesystem\CodeMetadataValue;
use MultiversX\SmartContracts\Typesystem\Types\BigUIntType;
use MultiversX\SmartContracts\Typesystem\Types\OptionalType;
use MultiversX\SmartContracts\Typesystem\Types\VariadicType;
use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\SmartContracts\Typesystem\TokenIdentifierValue;
use MultiversX\SmartContracts\Typesystem\Types\FieldDefinition;
use MultiversX\SmartContracts\Typesystem\Types\TokenIdentifierType;

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
        expect($this->serializer->nativeToString('biguint', '1234567890'))->toBe('biguint:1234567890');
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
        expect($result->getItems()[0])->toBeInstanceOf(StringValue::class);
        expect($result->getItems()[0]->valueOf())->toBe('hello');
        expect($result->getItems()[1])->toBeInstanceOf(StringValue::class);
        expect($result->getItems()[1]->valueOf())->toBe('world');
    });

    it('converts variadic to VariadicValue', function () {
        $result = $this->serializer->nativeToTyped('variadic:string', 'hello,world');
        expect($result)->toBeInstanceOf(VariadicValue::class);
        expect($result->getItems()[0])->toBeInstanceOf(StringValue::class);
        expect($result->getItems()[0]->valueOf())->toBe('hello');
        expect($result->getItems()[1])->toBeInstanceOf(StringValue::class);
        expect($result->getItems()[1]->valueOf())->toBe('world');
    });

    it('converts composite to CompositeValue', function () {
        $result = $this->serializer->nativeToTyped('composite:string|uint64|uint8', 'hello|12345678901234567890|255');
        expect($result)->toBeInstanceOf(CompositeValue::class);
        expect($result->getItems()[0])->toBeInstanceOf(StringValue::class);
        expect($result->getItems()[0]->valueOf())->toBe('hello');
        expect($result->getItems()[1])->toBeInstanceOf(U64Value::class);
        expect((string) $result->getItems()[1]->valueOf())->toBe('12345678901234567890');
        expect($result->getItems()[2])->toBeInstanceOf(U8Value::class);
        expect((string) $result->getItems()[2]->valueOf())->toBe('255');
    });

    it('converts string to StringValue', function () {
        $result = $this->serializer->nativeToTyped('string', 'hello');
        expect($result)->toBeInstanceOf(StringValue::class);
        expect($result->valueOf())->toBe('hello');
    });

    it('converts uint8 to U8Value', function () {
        $result = $this->serializer->nativeToTyped('uint8', 255);
        expect($result)->toBeInstanceOf(U8Value::class);
        expect((string) $result)->toBe('255');
    });

    it('converts uint16 to U16Value', function () {
        $result = $this->serializer->nativeToTyped('uint16', 65535);
        expect($result)->toBeInstanceOf(U16Value::class);
        expect((string) $result)->toBe('65535');
    });

    it('converts uint32 to U32Value', function () {
        $result = $this->serializer->nativeToTyped('uint32', 4294967295);
        expect($result)->toBeInstanceOf(U32Value::class);
        expect((string) $result)->toBe('4294967295');
    });

    it('converts uint64 to U64Value', function () {
        $result = $this->serializer->nativeToTyped('uint64', '18446744073709551615');
        expect($result)->toBeInstanceOf(U64Value::class);
        expect((string) $result)->toBe('18446744073709551615');
    });

    it('converts biguint to BigUIntValue', function () {
        $result = $this->serializer->nativeToTyped('biguint', '123456789012345678901234567890');
        expect($result)->toBeInstanceOf(BigUIntValue::class);
        expect((string) $result->valueOf())->toBe('123456789012345678901234567890');
    });

    it('converts bool to BooleanValue', function () {
        $result = $this->serializer->nativeToTyped('bool', true);
        expect($result)->toBeInstanceOf(BooleanValue::class);
        expect($result->valueOf())->toBeTrue();
    });

    it('converts address to AddressValue', function () {
        $address = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
        $result = $this->serializer->nativeToTyped('address', $address);
        expect($result)->toBeInstanceOf(AddressValue::class);
        expect($result->valueOf())->toBeInstanceOf(Address::class);
        expect($result->valueOf()->bech32())->toBe($address);
    });

    it('converts token to TokenIdentifierValue', function () {
        $result = $this->serializer->nativeToTyped('token', '1234');
        expect($result)->toBeInstanceOf(TokenIdentifierValue::class);
        expect($result->valueOf())->toBe('1234');
    });

    it('converts hex to BytesValue', function () {
        $result = $this->serializer->nativeToTyped('hex', '1234');
        expect($result)->toBeInstanceOf(BytesValue::class);
        expect(bin2hex($result->valueOf()))->toBe('1234');
    });

    it('converts codemeta to CodeMetadataValue', function () {
        $result = $this->serializer->nativeToTyped('codemeta', '0106');
        expect($result)->toBeInstanceOf(CodeMetadataValue::class);
        expect($result->valueOf())->toBeInstanceOf(CodeMetadata::class);
        expect(bin2hex($result->valueOf()->toBuffer()))->toBe('0106');
    });

    it('converts esdt to EsdtTokenPayment Struct', function () {
        $token = new Token(identifier: 'AAA-123456', nonce: BigInteger::of(5));
        $transfer = new TokenTransfer(token: $token, amount: BigInteger::of(100));
        $result = $this->serializer->nativeToTyped('esdt', $transfer);
        expect($result)->toBeInstanceOf(Struct::class);
        expect($result->getFieldValue('token_identifier'))->toBe('AAA-123456');
        expect($result->getFieldValue('token_nonce')->toInt())->toBe(5);
        expect((string) $result->getFieldValue('amount'))->toBe('100');
    });

    it('throws error for unsupported type', function () {
        expect(fn() => $this->serializer->nativeToTyped('unsupported', 'value'))
            ->toThrow(Exception::class, 'WarpArgSerializer (nativeToTyped): Unsupported input type: unsupported');
    });
});

describe('typedToNative', function () {
    it('converts OptionValue to native value', function () {
        $value = new OptionValue(new OptionType(new StringType()), StringValue::fromUTF8('abc'));
        $result = $this->serializer->typedToNative($value);
        expect($result)->toBe(['option:string', 'abc']);
    });

    it('converts OptionalValue to native value', function () {
        $result = $this->serializer->typedToNative(new OptionalValue(new OptionalType(new StringType), StringValue::fromUTF8('abc')));
        expect($result)->toBe(['optional:string', 'abc']);
    });

    it('converts ListValue to native value', function () {
        $value = new ListValue(new ListType(new StringType()), [
            StringValue::fromUTF8('abc'),
            StringValue::fromUTF8('def')
        ]);
        $result = $this->serializer->typedToNative($value);
        expect($result)->toBe(['list:string', 'abc,def']);
    });

    it('converts VariadicValue to native value', function () {
        $result = $this->serializer->typedToNative(
            new VariadicValue(
                new VariadicType(new StringType()),
                [StringValue::fromUTF8('abc'), StringValue::fromUTF8('def')]
            )
        );
        expect($result)->toBe(['variadic:string', 'abc,def']);
    });

    it('converts CompositeValue to native value', function () {
        $result = $this->serializer->typedToNative(
            new CompositeValue(
                new CompositeType(new StringType(), new U64Type()),
                [StringValue::fromUTF8('abc'), new U64Value('12345678901234567890')]
            )
        );
        expect($result)->toBe(['composite:string|uint64', 'abc|12345678901234567890']);
    });

    it('converts BigUIntValue to biguint', function () {
        $value = new BigUIntValue('123456789012345678901234567890');
        $result = $this->serializer->typedToNative($value);
        expect($result)->toBe(['biguint', '123456789012345678901234567890']);
    });

    it('converts U8Value to uint8', function () {
        $result = $this->serializer->typedToNative(new U8Value(255));
        expect($result)->toBe(['uint8', 255]);
    });

    it('converts U16Value to uint16', function () {
        $result = $this->serializer->typedToNative(new U16Value(65535));
        expect($result)->toBe(['uint16', 65535]);
    });

    it('converts U32Value to uint32', function () {
        $result = $this->serializer->typedToNative(new U32Value(4294967295));
        expect($result)->toBe(['uint32', 4294967295]);
    });

    it('converts U64Value to uint64', function () {
        [$type, $value] = $this->serializer->typedToNative(new U64Value('18446744073709551615'));
        expect($type)->toBe('uint64');
        expect((string) $value)->toBe('18446744073709551615');
    });

    it('converts StringValue to string', function () {
        $result = $this->serializer->typedToNative(StringValue::fromUTF8('hello'));
        expect($result)->toBe(['string', 'hello']);
    });

    it('converts BooleanValue to bool', function () {
        $result = $this->serializer->typedToNative(new BooleanValue(true));
        expect($result)->toBe(['bool', true]);
    });

    it('converts AddressValue to address', function () {
        $address = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
        $result = $this->serializer->typedToNative(new AddressValue(Address::newFromBech32($address)));
        expect($result)->toBe(['address', $address]);
    });

    it('converts TokenIdentifierValue to token', function () {
        $result = $this->serializer->typedToNative(new TokenIdentifierValue('1234'));
        expect($result)->toBe(['token', '1234']);
    });

    it('converts BytesValue to hex', function () {
        $result = $this->serializer->typedToNative(BytesValue::fromHex('1234'));
        expect($result)->toBe(['hex', '1234']);
    });

    it('converts CodeMetadataValue to codemeta', function () {
        $result = $this->serializer->typedToNative(
            new CodeMetadataValue(new CodeMetadata(true, false, true, true))
        );
        expect($result)->toBe(['codemeta', '0106']);
    });

    it('converts EsdtTokenPayment Struct to esdt', function () {
        $token = new Token(identifier: 'AAA-123456', nonce: BigInteger::of(5));
        $transfer = new TokenTransfer(token: $token, amount: BigInteger::of(100));
        list($type, $value) = $this->serializer->typedToNative(new Struct(
            new StructType('EsdtTokenPayment', [
                new FieldDefinition('token_identifier', '', new TokenIdentifierType()),
                new FieldDefinition('token_nonce', '', new U64Type()),
                new FieldDefinition('amount', '', new BigUIntType()),
            ]),
            [
                new Field(new TokenIdentifierValue($token->identifier), 'token_identifier'),
                new Field(new U64Value($token->nonce), 'token_nonce'),
                new Field(new BigUIntValue(BigInteger::of($transfer->amount)), 'amount'),
            ]
        ));
        expect($type)->toBe('esdt');
        expect($value->token->identifier)->toBe($token->identifier);
        expect($value->token->nonce->toInt())->toBe(5);
        expect((string) $value->amount)->toBe('100');
    });

    it('converts nested VariadicValue of CompositeValue to native value', function () {
        $result = $this->serializer->typedToNative(
            new VariadicValue(
                new VariadicType(new CompositeType(new StringType(), new U64Type())),
                [
                    new CompositeValue(
                        new CompositeType(new StringType(), new U64Type()),
                        [StringValue::fromUTF8('abc'), new U64Value(123)]
                    ),
                    new CompositeValue(
                        new CompositeType(new StringType(), new U64Type()),
                        [StringValue::fromUTF8('def'), new U64Value(456)]
                    ),
                    new CompositeValue(
                        new CompositeType(new StringType(), new U64Type()),
                        [StringValue::fromUTF8('ghi'), new U64Value(789)]
                    )
                ]
            )
        );
        expect($result)->toBe(['variadic:composite:string|uint64', 'abc|123,def|456,ghi|789']);
    });

    it('converts nested List of CompositeValue to native value', function () {
        $result = $this->serializer->typedToNative(
            new ListValue(
                new ListType(new CompositeType(new StringType(), new U64Type())),
                [
                    new CompositeValue(
                        new CompositeType(new StringType(), new U64Type()),
                        [StringValue::fromUTF8('abc'), new U64Value(123)]
                    ),
                    new CompositeValue(
                        new CompositeType(new StringType(), new U64Type()),
                        [StringValue::fromUTF8('def'), new U64Value(456)]
                    ),
                    new CompositeValue(
                        new CompositeType(new StringType(), new U64Type()),
                        [StringValue::fromUTF8('ghi'), new U64Value(789)]
                    )
                ]
            )
        );
        expect($result)->toBe(['list:composite:string|uint64', 'abc|123,def|456,ghi|789']);
    });
});

describe('stringToNative', function () {
    it('deserializes address values', function () {
        $address = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
        $result = $this->serializer->stringToNative("address:{$address}");
        expect($result)->toBe(['address', $address]);
    });

    it('deserializes bool values', function () {
        expect($this->serializer->stringToNative('bool:true'))->toEqual(['bool', true]);
        expect($this->serializer->stringToNative('bool:false'))->toEqual(['bool', false]);
    });

    it('deserializes biguint values', function () {
        $result = $this->serializer->stringToNative('biguint:1234567890');
        expect($result)->toEqual(['biguint', '1234567890']);
    });

    it('deserializes uint values', function () {
        expect($this->serializer->stringToNative('uint64:123'))->toBe(['uint64', 123]);
        expect($this->serializer->stringToNative('uint32:456'))->toBe(['uint32', 456]);
        expect($this->serializer->stringToNative('uint16:789'))->toBe(['uint16', 789]);
        expect($this->serializer->stringToNative('uint8:255'))->toBe(['uint8', 255]);
    });

    it('deserializes string values', function () {
        expect($this->serializer->stringToNative('string:hello'))->toBe(['string', 'hello']);
    });

    it('deserializes hex values', function () {
        expect($this->serializer->stringToNative('hex:0x1234'))->toBe(['hex', '0x1234']);
    });

    it('deserializes esdt values', function () {
        $result = $this->serializer->stringToNative('esdt:AAA-123456|5|100');
        $expected = [
            'esdt',
            new TokenTransfer(
                token: new Token(identifier: 'AAA-123456', nonce: BigInteger::of(5)),
                amount: BigInteger::of(100)
            )
        ];
        expect($result)->toEqual($expected);
    });
});

describe('stringToTyped', function () {
    it('converts string encoded value to StringValue', function () {
        $result = $this->serializer->stringToTyped('string:hello');
        expect($result)->toBeInstanceOf(StringValue::class);
        expect($result->valueOf())->toBe('hello');
    });

    it('converts uint8 encoded value to U8Value', function () {
        $uint8Result = $this->serializer->stringToTyped('uint8:255');
        expect($uint8Result)->toBeInstanceOf(U8Value::class);
        expect((string) $uint8Result)->toBe('255');
    });

    it('converts uint16 encoded value to U16Value', function () {
        $uint16Result = $this->serializer->stringToTyped('uint16:65535');
        expect($uint16Result)->toBeInstanceOf(U16Value::class);
        expect((string) $uint16Result)->toBe('65535');
    });

    it('converts uint32 encoded value to U32Value', function () {
        $uint32Result = $this->serializer->stringToTyped('uint32:4294967295');
        expect($uint32Result)->toBeInstanceOf(U32Value::class);
        expect((string) $uint32Result)->toBe('4294967295');
    });

    it('converts uint64 encoded value to U64Value', function () {
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
        expect(bin2hex($result->valueOf()))->toBe('1234');
    });

    it('throws error for unsupported type', function () {
        expect(fn() => $this->serializer->stringToTyped('unsupported:value'))
            ->toThrow(Exception::class, 'WarpArgSerializer (nativeToTyped): Unsupported input type: unsupported');
    });
});
