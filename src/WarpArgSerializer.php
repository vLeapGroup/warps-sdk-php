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

const ParamsSeparator = ':';
const CompositeSeparator = '|';

class WarpArgSerializer
{
    public function nativeToString(string $type, mixed $value): string
    {
        if ($type === 'esdt' && $value instanceof TokenTransfer) {
            return "esdt:{$value->token->identifier}|{$value->token->nonce}|{$value->amount}";
        }
        if ($type === 'bool') {
            return "{$type}:" . ($value ? 'true' : 'false');
        }
        return "{$type}:" . ($value ?? '');
    }

    public function nativeToTyped(string $type, mixed $value): TypedValue
    {
        $stringValue = $this->nativeToString($type, $value);
        return $this->stringToTyped($stringValue);
    }

    public function typedToNative(TypedValue $value): array
    {
        $stringValue = $this->typedToString($value);
        return $this->stringToNative($stringValue);
    }

    public function typedToString(TypedValue $value): string
    {
        if ($value->hasClassOrSuperclass(OptionValue::ClassName)) {
            if (!($value instanceof OptionValue && $value->isSet())) {
                return 'option:null';
            }
            $result = $this->typedToString($value->getTypedValue());
            return "option:{$result}";
        }
        if ($value->hasClassOrSuperclass(OptionalValue::ClassName)) {
            if (!($value instanceof OptionalValue && $value->isSet())) {
                return 'optional:null';
            }
            $result = $this->typedToString($value->getTypedValue());
            return "optional:{$result}";
        }
        if ($value->hasClassOrSuperclass(ListValue::ClassName)) {
            $items = $value->getItems();
            $types = array_map(fn($item) => explode(ParamsSeparator, $this->typedToString($item))[0], $items);
            $type = $types[0];
            $values = array_map(fn($item) => explode(ParamsSeparator, $this->typedToString($item))[1], $items);
            return "list:{$type}:" . implode(',', $values);
        }
        if ($value->hasClassOrSuperclass(VariadicValue::ClassName)) {
            $items = $value->getItems();
            $types = array_map(fn($item) => explode(ParamsSeparator, $this->typedToString($item))[0], $items);
            $type = $types[0];
            $values = array_map(fn($item) => explode(ParamsSeparator, $this->typedToString($item))[1], $items);
            return "variadic:{$type}:" . implode(',', $values);
        }
        if ($value->hasClassOrSuperclass(CompositeValue::ClassName)) {
            $items = $value->getItems();
            $types = array_map(fn($item) => explode(ParamsSeparator, $this->typedToString($item))[0], $items);
            $values = array_map(fn($item) => explode(ParamsSeparator, $this->typedToString($item))[1], $items);
            $rawTypes = implode(CompositeSeparator, $types);
            $rawValues = implode(CompositeSeparator, $values);
            return "composite({$rawTypes}):{$rawValues}";
        }
        if ($value->hasClassOrSuperclass(BigUIntValue::ClassName)) {
            return 'biguint:' . BigInteger::of($value->valueOf());
        }
        if ($value->hasClassOrSuperclass(U8Value::ClassName)) {
            return 'uint8:' . $value->valueOf();
        }
        if ($value->hasClassOrSuperclass(U16Value::ClassName)) {
            return 'uint16:' . $value->valueOf();
        }
        if ($value->hasClassOrSuperclass(U32Value::ClassName)) {
            return 'uint32:' . $value->valueOf();
        }
        if ($value->hasClassOrSuperclass(U64Value::ClassName)) {
            return 'uint64:' . BigInteger::of($value->valueOf());
        }
        if ($value->hasClassOrSuperclass(StringValue::ClassName)) {
            return 'string:' . $value->valueOf();
        }
        if ($value->hasClassOrSuperclass(BooleanValue::ClassName)) {
            return 'bool:' . ($value->valueOf() ? 'true' : 'false');
        }
        if ($value->hasClassOrSuperclass(AddressValue::ClassName)) {
            return 'address:' . $value->valueOf()->bech32();
        }
        if ($value->hasClassOrSuperclass(TokenIdentifierValue::ClassName)) {
            return 'token:' . $value->valueOf();
        }
        if ($value->hasClassOrSuperclass(BytesValue::ClassName)) {
            return 'hex:' . bin2hex($value->valueOf());
        }
        if ($value->hasClassOrSuperclass(CodeMetadataValue::ClassName)) {
            return 'codemeta:' . bin2hex($value->valueOf()->toBuffer());
        }
        if ($value->getType()->getName() === 'EsdtTokenPayment') {
            $identifier = $value->getFieldValue('token_identifier');
            $nonce = $value->getFieldValue('token_nonce');
            $amount = $value->getFieldValue('amount');
            return "esdt:{$identifier}|{$nonce}|{$amount}";
        }

        throw new \Exception("WarpArgSerializer (typedToString): Unsupported input type: " . $value->getClassName());
    }

    public function stringToNative(string $value): array
    {
        $parts = explode(ParamsSeparator, $value);
        $baseType = $parts[0];
        $val = implode(ParamsSeparator, array_slice($parts, 1));

        if ($baseType === 'option') {
            [$baseType, $baseValue] = explode(ParamsSeparator, $val);
            return ["option:{$baseType}", $baseValue ?: null];
        }
        if ($baseType === 'optional') {
            [$baseType, $baseValue] = explode(ParamsSeparator, $val);
            return ["optional:{$baseType}", $baseValue ?: null];
        }
        if ($baseType === 'list') {
            $listParts = explode(ParamsSeparator, $val);
            $baseType = implode(ParamsSeparator, array_slice($listParts, 0, -1));
            $valuesRaw = end($listParts);
            $valuesStrings = $valuesRaw ? explode(',', $valuesRaw) : [];
            $values = array_map(fn($v) => $this->stringToNative("{$baseType}:{$v}")[1], $valuesStrings);
            return ["list:{$baseType}", $values];
        }
        if ($baseType === 'variadic') {
            $variadicParts = explode(ParamsSeparator, $val);
            $baseType = implode(ParamsSeparator, array_slice($variadicParts, 0, -1));
            $valuesRaw = end($variadicParts);
            $valuesStrings = $valuesRaw ? explode(',', $valuesRaw) : [];
            $values = array_map(fn($v) => $this->stringToNative("{$baseType}:{$v}")[1], $valuesStrings);
            return ["variadic:{$baseType}", $values];
        }
        if (str_starts_with($baseType, 'composite')) {
            preg_match('/\(([^)]+)\)/', $baseType, $matches);
            $rawTypes = explode(CompositeSeparator, $matches[1]);
            $valuesStrings = explode(CompositeSeparator, $val);
            $values = array_map(
                fn($val, $index) => $this->stringToNative("{$rawTypes[$index]}:{$val}")[1],
                $valuesStrings,
                array_keys($valuesStrings)
            );
            return [$baseType, $values];
        }
        if ($baseType === 'string') {
            return [$baseType, $val];
        }
        if (in_array($baseType, ['uint8', 'uint16', 'uint32'])) {
            return [$baseType, (int)$val];
        }
        if ($baseType === 'uint64' || $baseType === 'biguint') {
            return [$baseType, BigInteger::of($val ?: '0')];
        }
        if ($baseType === 'bool') {
            return [$baseType, $val === 'true'];
        }
        if ($baseType === 'address') {
            return [$baseType, $val];
        }
        if ($baseType === 'token') {
            return [$baseType, $val];
        }
        if ($baseType === 'hex') {
            return [$baseType, $val];
        }
        if ($baseType === 'codemeta') {
            return [$baseType, $val];
        }
        if ($baseType === 'esdt') {
            [$identifier, $nonce, $amount] = explode(CompositeSeparator, $val);
            return [$baseType, new TokenTransfer(
                token: new Token(identifier: $identifier, nonce: BigInteger::of($nonce)),
                amount: BigInteger::of($amount)
            )];
        }

        throw new \Exception("WarpArgSerializer (stringToNative): Unsupported input type: {$baseType}");
    }

    public function stringToTyped(string $value): TypedValue
    {
        $parts = explode(ParamsSeparator, $value);
        $type = $parts[0];
        $val = implode(ParamsSeparator, array_slice($parts, 1));

        if ($type === 'option') {
            $baseValue = $this->stringToTyped($val);
            return $baseValue instanceof NothingValue
                ? OptionValue::newMissingTyped($baseValue->getType())
                : OptionValue::newProvided($baseValue);
        }
        if ($type === 'optional') {
            $baseValue = $this->stringToTyped($val);
            return $baseValue instanceof NothingValue
                ? OptionalValue::newMissing()
                : new OptionalValue(new OptionalType($baseValue->getType()), $baseValue);
        }
        if ($type === 'list') {
            [$baseType, $listValues] = explode(ParamsSeparator, $val, 2);
            $values = $listValues ? explode(',', $listValues) : [];
            $typedValues = array_map(fn($v) => $this->stringToTyped("{$baseType}:{$v}"), $values);
            return new ListValue(new ListType($this->nativeToType($baseType)), $typedValues);
        }
        if ($type === 'variadic') {
            [$baseType, $listValues] = explode(ParamsSeparator, $val, 2);
            $values = $listValues ? explode(',', $listValues) : [];
            $typedValues = array_map(fn($v) => $this->stringToTyped("{$baseType}:{$v}"), $values);
            return new VariadicValue(new VariadicType($this->nativeToType($baseType)), $typedValues);
        }
        if (str_starts_with($type, 'composite')) {
            preg_match('/\(([^)]+)\)/', $type, $matches);
            $baseType = $matches[1];
            $rawValues = explode(CompositeSeparator, $val);
            $rawTypes = explode(CompositeSeparator, $baseType);
            $values = array_map(
                fn($val, $i) => $this->stringToTyped("{$rawTypes[$i]}:{$val}"),
                $rawValues,
                array_keys($rawValues)
            );
            $types = array_map(fn($v) => $v->getType(), $values);
            return new CompositeValue(new CompositeType(...$types), $values);
        }
        if ($type === 'string') {
            return $val ? StringValue::fromUTF8($val) : new NothingValue();
        }
        if ($type === 'uint8') {
            return $val ? new U8Value((int)$val) : new NothingValue();
        }
        if ($type === 'uint16') {
            return $val ? new U16Value((int)$val) : new NothingValue();
        }
        if ($type === 'uint32') {
            return $val ? new U32Value((int)$val) : new NothingValue();
        }
        if ($type === 'uint64') {
            return $val ? new U64Value(BigInteger::of($val)) : new NothingValue();
        }
        if ($type === 'biguint') {
            return $val ? new BigUIntValue(BigInteger::of($val)) : new NothingValue();
        }
        if ($type === 'bool') {
            return new BooleanValue(is_bool($val) ? $val : $val === 'true');
        }
        if ($type === 'address') {
            return $val ? new AddressValue(Address::newFromBech32($val)) : new NothingValue();
        }
        if ($type === 'token') {
            return $val ? new TokenIdentifierValue($val) : new NothingValue();
        }
        if ($type === 'hex') {
            return $val ? BytesValue::fromHex($val) : new NothingValue();
        }
        if ($type === 'codemeta') {
            return new CodeMetadataValue(CodeMetadata::fromBuffer(hex2bin($val)));
        }
        if ($type === 'esdt') {
            $parts = explode(CompositeSeparator, $val);
            return new Struct($this->nativeToType('esdt'), [
                new Field(new TokenIdentifierValue($parts[0]), 'token_identifier'),
                new Field(new U64Value(BigInteger::of($parts[1])), 'token_nonce'),
                new Field(new BigUIntValue(BigInteger::of($parts[2])), 'amount'),
            ]);
        }

        throw new \Exception("WarpArgSerializer (stringToTyped): Unsupported input type: {$type}");
    }

    public function nativeToType(string $type): Type
    {
        if (str_starts_with($type, 'composite')) {
            preg_match('/\(([^)]+)\)/', $type, $matches);
            $rawTypes = explode(CompositeSeparator, $matches[1]);
            return new CompositeType(...array_map(fn($t) => $this->nativeToType($t), $rawTypes));
        }
        if ($type === 'string') return new StringType();
        if ($type === 'uint8') return new U8Type();
        if ($type === 'uint16') return new U16Type();
        if ($type === 'uint32') return new U32Type();
        if ($type === 'uint64') return new U64Type();
        if ($type === 'biguint') return new BigUIntType();
        if ($type === 'bool') return new BooleanType();
        if ($type === 'address') return new AddressType();
        if ($type === 'token') return new TokenIdentifierType();
        if ($type === 'hex') return new BytesType();
        if ($type === 'codemeta') return new CodeMetadataType();
        if ($type === 'esdt' || $type === 'nft') {
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
        if ($type instanceof StructType && $type->getClassName() === 'EsdtTokenPayment') return 'esdt';

        throw new \Exception("WarpArgSerializer (typeToNative): Unsupported input type: " . $type->getClassName());
    }
}
