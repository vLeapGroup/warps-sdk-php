<?php

use function Vleap\Warps\{
    address,
    biguint,
    boolean,
    codemeta,
    composite_of,
    hex,
    list_of,
    nothing,
    option_of,
    optional_pf,
    string,
    token,
    u16,
    u32,
    u64,
    u8,
    variadic_of
};

use MultiversX\Address;
use MultiversX\SmartContracts\CodeMetadata;
use MultiversX\SmartContracts\Typesystem\AddressValue;
use MultiversX\SmartContracts\Typesystem\BigUIntValue;
use MultiversX\SmartContracts\Typesystem\BooleanValue;
use MultiversX\SmartContracts\Typesystem\BytesValue;
use MultiversX\SmartContracts\Typesystem\CodeMetadataValue;
use MultiversX\SmartContracts\Typesystem\CompositeValue;
use MultiversX\SmartContracts\Typesystem\ListValue;
use MultiversX\SmartContracts\Typesystem\NothingValue;
use MultiversX\SmartContracts\Typesystem\OptionalValue;
use MultiversX\SmartContracts\Typesystem\OptionValue;
use MultiversX\SmartContracts\Typesystem\StringValue;
use MultiversX\SmartContracts\Typesystem\TokenIdentifierValue;
use MultiversX\SmartContracts\Typesystem\U16Value;
use MultiversX\SmartContracts\Typesystem\U32Value;
use MultiversX\SmartContracts\Typesystem\U64Value;
use MultiversX\SmartContracts\Typesystem\U8Value;
use MultiversX\SmartContracts\Typesystem\VariadicValue;

describe('Codec Utilities', function () {
    describe('option', function () {
        it('creates an OptionValue with value', function () {
            $stringValue = string('hello');
            $result = option_of($stringValue);
            expect($result)->toBeInstanceOf(OptionValue::class);
            expect($result->valueOf())->toBe('hello');
        });

        it('creates an OptionValue with missing value', function () {
            $result = option_of(null);
            expect($result)->toBeInstanceOf(OptionValue::class);
            expect($result->isSet())->toBeFalse();
        });
    });

    describe('optional', function () {
        it('creates an OptionalValue with value', function () {
            $stringValue = string('hello');
            $result = optional_pf($stringValue);
            expect($result)->toBeInstanceOf(OptionalValue::class);
            expect($result->valueOf())->toBe('hello');
        });

        it('creates an OptionalValue with missing value', function () {
            $result = optional_pf(null);
            expect($result)->toBeInstanceOf(OptionalValue::class);
            expect($result->isSet())->toBeFalse();
        });
    });

    describe('list_of', function () {
        it('creates a List', function () {
            $stringValue1 = string('hello');
            $stringValue2 = string('world');
            $result = list_of([$stringValue1, $stringValue2]);
            expect($result)->toBeInstanceOf(ListValue::class);
            expect($result->getItems()[0])->toBeInstanceOf(StringValue::class);
            expect($result->getItems()[0]->valueOf())->toBe('hello');
            expect($result->getItems()[1])->toBeInstanceOf(StringValue::class);
            expect($result->getItems()[1]->valueOf())->toBe('world');
        });
    });

    describe('variadic', function () {
        it('creates a VariadicValue', function () {
            $stringValue1 = string('hello');
            $stringValue2 = string('world');
            $result = variadic_of([$stringValue1, $stringValue2]);
            expect($result)->toBeInstanceOf(VariadicValue::class);
            expect($result->getItems()[0])->toBeInstanceOf(StringValue::class);
            expect($result->getItems()[0]->valueOf())->toBe('hello');
            expect($result->getItems()[1])->toBeInstanceOf(StringValue::class);
            expect($result->getItems()[1]->valueOf())->toBe('world');
        });
    });

    describe('composite', function () {
        it('creates a CompositeValue', function () {
            $stringValue = string('hello');
            $u64Value = u64('12345678901234567890');
            $result = composite_of([$stringValue, $u64Value]);
            expect($result)->toBeInstanceOf(CompositeValue::class);
            expect($result->getItems()[0])->toBeInstanceOf(StringValue::class);
            expect($result->getItems()[0]->valueOf())->toBe('hello');
            expect($result->getItems()[1])->toBeInstanceOf(U64Value::class);
            expect((string) $result->getItems()[1]->valueOf())->toBe('12345678901234567890');
        });
    });

    describe('string', function () {
        it('creates a StringValue', function () {
            $result = string('hello');
            expect($result)->toBeInstanceOf(StringValue::class);
            expect($result->valueOf())->toBe('hello');
        });
    });

    describe('u8', function () {
        it('creates a U8Value', function () {
            $result = u8(255);
            expect($result)->toBeInstanceOf(U8Value::class);
            expect((string) $result->valueOf())->toBe('255');
        });
    });

    describe('u16', function () {
        it('creates a U16Value', function () {
            $result = u16(65535);
            expect($result)->toBeInstanceOf(U16Value::class);
            expect((string) $result->valueOf())->toBe('65535');
        });
    });

    describe('u32', function () {
        it('creates a U32Value', function () {
            $result = u32(4294967295);
            expect($result)->toBeInstanceOf(U32Value::class);
            expect((string) $result->valueOf())->toBe('4294967295');
        });
    });

    describe('u64', function () {
        it('creates a U64Value', function () {
            $value = '18446744073709551615';
            $result = u64($value);
            expect($result)->toBeInstanceOf(U64Value::class);
            expect((string) $result->valueOf())->toBe($value);
        });
    });

    describe('biguint', function () {
        it('creates a BigUIntValue', function () {
            $value = '123456789012345678901234567890';
            $result = biguint($value);
            expect($result)->toBeInstanceOf(BigUIntValue::class);
            expect((string) $result->valueOf())->toBe($value);
        });
    });

    describe('boolean', function () {
        it('creates a BooleanValue', function () {
            $resultTrue = boolean(true);
            expect($resultTrue)->toBeInstanceOf(BooleanValue::class);
            expect($resultTrue->valueOf())->toBeTrue();

            $resultFalse = boolean(false);
            expect($resultFalse)->toBeInstanceOf(BooleanValue::class);
            expect($resultFalse->valueOf())->toBeFalse();
        });
    });

    describe('address', function () {
        it('creates an AddressValue', function () {
            $addrStr = 'erd1qqqqqqqqqqqqqqqpqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqplllst77y4l';
            $result = address($addrStr);
            expect($result)->toBeInstanceOf(AddressValue::class);
            expect($result->valueOf())->toBeInstanceOf(Address::class);
            expect($result->valueOf()->bech32())->toBe($addrStr);
        });
    });

    describe('token', function () {
        it('creates a TokenIdentifierValue', function () {
            $result = token('1234');
            expect($result)->toBeInstanceOf(TokenIdentifierValue::class);
            expect($result->valueOf())->toBe('1234');
        });
    });

    describe('hex', function () {
        it('creates a BytesValue', function () {
            $hexString = '1234';
            $result = hex($hexString);
            expect($result)->toBeInstanceOf(BytesValue::class);
            expect(bin2hex($result->valueOf()))->toBe($hexString);
        });
    });

    describe('codemeta', function () {
        it('creates a CodeMetadataValue', function () {
            $hexString = '0106';
            $result = codemeta($hexString);
            expect($result)->toBeInstanceOf(CodeMetadataValue::class);
            expect($result->valueOf())->toBeInstanceOf(CodeMetadata::class);
            expect(bin2hex($result->valueOf()->toBuffer()))->toBe($hexString);
        });
    });

    describe('nothing', function () {
        it('creates a NothingValue', function () {
            $result = nothing();
            expect($result)->toBeInstanceOf(NothingValue::class);
        });
    });
});
