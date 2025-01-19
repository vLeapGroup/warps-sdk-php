<?php

namespace Vleap\Warps;

use MultiversX\Token;
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
use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\OptionValue;
use MultiversX\SmartContracts\Typesystem\StringValue;
use MultiversX\SmartContracts\Typesystem\AddressValue;
use MultiversX\SmartContracts\Typesystem\BigUIntValue;
use MultiversX\SmartContracts\Typesystem\BooleanValue;
use MultiversX\SmartContracts\Typesystem\NothingValue;
use MultiversX\SmartContracts\Typesystem\Types\U8Type;
use MultiversX\SmartContracts\Typesystem\OptionalValue;
use MultiversX\SmartContracts\Typesystem\Types\U16Type;
use MultiversX\SmartContracts\Typesystem\Types\U32Type;
use MultiversX\SmartContracts\Typesystem\Types\U64Type;
use MultiversX\SmartContracts\Typesystem\VariadicValue;
use MultiversX\SmartContracts\Typesystem\CompositeValue;
use MultiversX\SmartContracts\Typesystem\Types\ListType;
use MultiversX\SmartContracts\Typesystem\Types\BytesType;
use MultiversX\SmartContracts\Typesystem\Types\OptionType;
use MultiversX\SmartContracts\Typesystem\Types\StringType;
use MultiversX\SmartContracts\Typesystem\Types\StructType;
use MultiversX\SmartContracts\Typesystem\CodeMetadataValue;
use MultiversX\SmartContracts\Typesystem\Types\AddressType;
use MultiversX\SmartContracts\Typesystem\Types\BigUIntType;
use MultiversX\SmartContracts\Typesystem\Types\BooleanType;
use MultiversX\SmartContracts\Typesystem\Types\VariadicType;
use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\SmartContracts\Typesystem\TokenIdentifierValue;
use MultiversX\SmartContracts\Typesystem\Types\FieldDefinition;
use MultiversX\SmartContracts\Typesystem\Types\CodeMetadataType;
use MultiversX\SmartContracts\Typesystem\Types\OptionalType;
use MultiversX\SmartContracts\Typesystem\Types\TokenIdentifierType;

class WarpArgSerializer
{
    public function nativeToString(string $type, mixed $value): string
    {
        if ($type === 'esdt' && $value instanceof TokenTransfer) {
            return "esdt:{$value->token->identifier}|{$value->token->nonce}|{$value->amount}";
        } else if ($type === 'bool') {
            return 'bool:' . ($value ? 'true' : 'false');
        }

        return "{$type}:" . ($value ? (string)$value : '');
    }

    public function nativeToTyped(string $type, mixed $value): TypedValue
    {
        $typeParts = explode(':', $type);
        $baseType = $typeParts[0];

        if ($baseType === 'option') {
            $baseType = $typeParts[1];
            $baseValue = $this->nativeToTyped($baseType, $value);
            return $value ? OptionValue::newProvided($baseValue) : OptionValue::newMissingTyped($baseValue->getType());
        }
        else if ($baseType === 'optional') {
            $baseType = $typeParts[1];
            $baseValue = $this->nativeToTyped($baseType, $value);
            return $value ? new OptionalValue(new OptionalType($baseValue->getType()), $baseValue) : OptionalValue::newMissing();
        }
        else if ($baseType === 'list') {
            $baseType = $typeParts[1];
            $values = $value ? explode(',', $value) : [];
            $typedValues = array_map(fn($val) => $this->nativeToTyped($baseType, $val), $values);
            return new ListValue(new ListType($this->nativeToType($baseType)), $typedValues);
        }
        else if ($baseType === 'variadic') {
            $baseTypeNative = $typeParts[1];
            $baseType = $this->nativeToType($baseTypeNative);
            $values = $value ? explode(',', $value) : [];
            $typedValues = array_map(fn($val) => $this->nativeToTyped($baseTypeNative, $val), $values);
            return new VariadicValue(new VariadicType($baseType), $typedValues);
        }
        else if ($baseType === 'composite') {
            list(, $baseType) = explode(':', $type);
            $rawValues = explode('|', $value);
            $rawTypes = explode('|', $baseType);
            $values = array_map(fn($val, $index) => $this->nativeToTyped($rawTypes[$index], $val), $rawValues, array_keys($rawValues));
            $types = array_map(fn($type) => $this->nativeToType($type), $rawTypes);
            return new CompositeValue(new CompositeType(...$types), $values);
        }

        switch ($type) {
            case 'string':
                return $value ? StringValue::fromUTF8($value) : new NothingValue();
            case 'uint8':
                return $value ? new U8Value((int)$value) : new NothingValue();
            case 'uint16':
                return $value ? new U16Value((int)$value) : new NothingValue();
            case 'uint32':
                return $value ? new U32Value((int)$value) : new NothingValue();
            case 'uint64':
                return $value ? new U64Value(BigInteger::of($value)) : new NothingValue();
            case 'biguint':
                return $value ? new BigUIntValue(BigInteger::of($value)) : new NothingValue();
            case 'bool':
                return $value ? new BooleanValue($value === true || $value === 'true') : new NothingValue();
            case 'address':
                return $value ? new AddressValue(Address::newFromBech32($value)) : new NothingValue();
            case 'token':
                return $value ? new TokenIdentifierValue($value) : new NothingValue();
            case 'hex':
                return $value ? BytesValue::fromHex($value) : new NothingValue();
            case 'codemeta':
                return new CodeMetadataValue(CodeMetadata::fromBuffer(hex2bin($value)));
            case 'esdt':
                return new Struct($this->nativeToType('esdt'), [
                    new Field(new TokenIdentifierValue($value->token->identifier), 'token_identifier'),
                    new Field(new U64Value($value->token->nonce), 'token_nonce'),
                    new Field(new BigUIntValue($value->amount), 'amount'),
                ]);
        }

        throw new \Exception("WarpArgSerializer (nativeToTyped): Unsupported input type: {$type}");
    }

    public function typedToNative(TypedValue $value): array
    {
        if ($value->hasClassOrSuperclass(OptionValue::ClassName)) {
            if (!$value->isSet()) {
                return ['option', null];
            }
            [$type, $val] = $this->typedToNative($value->getTypedValue());
            return ["option:{$type}", $val];
        }

        if ($value->hasClassOrSuperclass(OptionalValue::ClassName)) {
            if (!$value->isSet()) {
                return ['optional', null];
            }
            [$type, $val] = $this->typedToNative($value->getTypedValue());
            return ["optional:{$type}", $val];
        }

        if ($value->hasClassOrSuperclass(ListValue::ClassName)) {
            $items = $value->getItems();
            $types = array_map(fn($item) => $this->typedToNative($item)[0], $items);
            $type = $types[0];
            $values = array_map(fn($item) => $this->typedToNative($item)[1], $items);
            return ["list:{$type}", implode(',', $values)];
        }

        if ($value->hasClassOrSuperclass(VariadicValue::ClassName)) {
            $items = $value->getItems();
            $types = array_map(fn($item) => $this->typedToNative($item)[0], $items);
            $type = $types[0];
            $values = array_map(fn($item) => $this->typedToNative($item)[1], $items);
            return ["variadic:{$type}", implode(',', $values)];
        }

        if ($value->hasClassOrSuperclass(CompositeValue::ClassName)) {
            $items = $value->getItems();
            $types = array_map(fn($item) => $this->typeToNative($item->getType()), $items);
            $values = array_map(fn($item) => $item->valueOf(), $items);
            $rawTypes = implode('|', $types);
            $rawValues = implode('|', $values);
            return ["composite:{$rawTypes}", $rawValues];
        }

        if ($value->hasClassOrSuperclass(BigUIntValue::ClassName)) {
            return ['biguint', (string) $value->valueOf()];
        }

        if ($value->hasClassOrSuperclass(U8Value::ClassName)) {
            return ['uint8', $value->valueOf()->toInt()];
        }

        if ($value->hasClassOrSuperclass(U16Value::ClassName)) {
            return ['uint16', $value->valueOf()->toInt()];
        }

        if ($value->hasClassOrSuperclass(U32Value::ClassName)) {
            return ['uint32', $value->valueOf()->toInt()];
        }

        if ($value->hasClassOrSuperclass(U64Value::ClassName)) {
            return ['uint64', (string) $value->valueOf()];
        }

        if ($value->hasClassOrSuperclass(StringValue::ClassName)) {
            return ['string', $value->valueOf()];
        }

        if ($value->hasClassOrSuperclass(BooleanValue::ClassName)) {
            return ['bool', $value->valueOf()];
        }

        if ($value->hasClassOrSuperclass(AddressValue::ClassName)) {
            return ['address', $value->valueOf()->bech32()];
        }

        if ($value->hasClassOrSuperclass(TokenIdentifierValue::ClassName)) {
            return ['token', $value->valueOf()];
        }

        if ($value->hasClassOrSuperclass(BytesValue::ClassName)) {
            return ['hex', bin2hex($value->valueOf())];
        }

        if ($value->hasClassOrSuperclass(CodeMetadataValue::ClassName)) {
            return ['codemeta', bin2hex($value->valueOf()->toBuffer())];
        }

        if ($value->getType()->getName() === 'EsdtTokenPayment') {
            $identifier = $value->getFieldValue('token_identifier');
            $nonce = $value->getFieldValue('token_nonce');
            $amount = $value->getFieldValue('amount');
            $token = new Token(identifier: $identifier, nonce: BigInteger::of($nonce));
            return ['esdt', new TokenTransfer(token: $token, amount: BigInteger::of($amount))];
        }

        throw new \Exception("WarpArgSerializer (typedToNative): Unsupported input type: " . $value->getClassName());
    }

    public function typedToString(TypedValue $value): string
    {
        [$type, $val] = $this->typedToNative($value);
        return $this->nativeToString($type, $val);
    }

    public function stringToNative(string $value): array
    {
        $parts = explode(':', $value);
        $baseType = $parts[0];
        $val = implode(':', array_slice($parts, 1));

        if ($baseType === 'option') {
            list($baseType, $baseValue) = explode(':', $val);
            return ["option:{$baseType}", $baseValue ?: null];
        } elseif ($baseType === 'optional') {
            list($baseType, $baseValue) = explode(':', $val);
            return ["optional:{$baseType}", $baseValue ?: null];
        } elseif ($baseType === 'list') {
            $listParts = explode(':', $val);
            $baseType = implode(':', array_slice($listParts, 0, -1));
            $valuesRaw = end($listParts);
            $valuesStrings = $valuesRaw ? explode(',', $valuesRaw) : [];
            $values = array_map(fn($v) => $this->stringToNative("{$baseType}:{$v}")[1], $valuesStrings);
            return ["list:{$baseType}", $values];
        } elseif ($baseType === 'variadic') {
            $variadicParts = explode(':', $val);
            $baseType = implode(':', array_slice($variadicParts, 0, -1));
            $valuesRaw = end($variadicParts);
            $valuesStrings = $valuesRaw ? explode(',', $valuesRaw) : [];
            $values = array_map(fn($v) => $this->stringToNative("{$baseType}:{$v}")[1], $valuesStrings);
            return ["variadic:{$baseType}", $values];
        } elseif ($baseType === 'composite') {
            list($baseType, $valuesRaw) = explode(':', $val);
            $rawTypes = explode('|', $baseType);
            $valuesStrings = explode('|', $valuesRaw);
            $values = array_map(fn($val, $index) => $this->stringToNative("{$rawTypes[$index]}:{$val}")[1], $valuesStrings, array_keys($valuesStrings));
            return ["composite:{$baseType}", $values];
        } elseif ($baseType === 'string') {
            return [$baseType, $val];
        } elseif (in_array($baseType, ['uint8', 'uint16', 'uint32'])) {
            return [$baseType, (int) $val];
        } elseif ($baseType === 'uint64' || $baseType === 'biguint') {
            return [$baseType, BigInteger::of($val ?: '0')];
        } elseif ($baseType === 'bool') {
            return [$baseType, $val === 'true'];
        } elseif ($baseType === 'address') {
            return [$baseType, $val];
        } elseif ($baseType === 'token') {
            return [$baseType, $val];
        } elseif ($baseType === 'hex') {
            return [$baseType, $val];
        } elseif ($baseType === 'codemeta') {
            return [$baseType, $val];
        } elseif ($baseType === 'esdt') {
            list($identifier, $nonce, $amount) = explode('|', $val);
            return [$baseType, new TokenTransfer(
                token: new Token(identifier: $identifier, nonce: BigInteger::of($nonce)),
                amount: BigInteger::of($amount)
            )];
        }

        throw new \Exception("WarpArgSerializer (stringToNative): Unsupported input type: {$baseType}");
    }

    public function stringToTyped(string $value): TypedValue
    {
        [$type, $val] = explode(':', $value, 2);

        return $this->nativeToTyped($type, $val);
    }

    public function nativeToType(string $type): Type
    {
        switch ($type) {
            case 'string':
                return new StringType();
            case 'uint8':
                return new U8Type();
            case 'uint16':
                return new U16Type();
            case 'uint32':
                return new U32Type();
            case 'uint64':
                return new U64Type();
            case 'biguint':
                return new BigUIntType();
            case 'bool':
                return new BooleanType();
            case 'address':
                return new AddressType();
            case 'token':
                return new TokenIdentifierType();
            case 'hex':
                return new BytesType();
            case 'codemeta':
                return new CodeMetadataType();
            case 'esdt':
            case 'nft':
                return new StructType('EsdtTokenPayment', [
                    new FieldDefinition('token_identifier', '', new TokenIdentifierType()),
                    new FieldDefinition('token_nonce', '', new U64Type()),
                    new FieldDefinition('amount', '', new BigUIntType()),
                ]);
        }

        throw new \Exception("WarpArgSerializer (nativeToType): Unsupported input type: {$type}");
    }

    public function typeToNative(Type $type): string
    {
        if ($type instanceof StringType) return 'string';
        if ($type instanceof U8Type) return 'uint8';
        if ($type instanceof U16Type) return 'uint16';
        if ($type instanceof U32Type) return 'uint32';
        if ($type instanceof U64Type) return 'uint64';
        if ($type instanceof BigUIntType) return 'biguint';
        if ($type instanceof BooleanType) return 'bool';
        if ($type instanceof AddressType) return 'address';
        if ($type instanceof TokenIdentifierType) return 'token';
        if ($type instanceof BytesType) return 'hex';
        if ($type instanceof CodeMetadataType) return 'codemeta';
        if ($type instanceof StructType && $type->getName() === 'EsdtTokenPayment') return 'esdt';

        throw new \Exception("WarpArgSerializer (typeToNative): Unsupported input type: " . $type->getName());
    }
}
