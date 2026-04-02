<?php

use Vleap\Warps\WarpI18n;

it('returns a plain string unchanged for any locale', function () {
    expect(WarpI18n::resolve('Hello', 'en'))->toBe('Hello');
    expect(WarpI18n::resolve('Hello', 'de'))->toBe('Hello');
    expect(WarpI18n::resolve('Hello', 'fr'))->toBe('Hello');
});

it('resolves array text by matching locale', function () {
    $text = ['en' => 'Hello', 'de' => 'Hallo', 'fr' => 'Bonjour'];

    expect(WarpI18n::resolve($text, 'en'))->toBe('Hello');
    expect(WarpI18n::resolve($text, 'de'))->toBe('Hallo');
    expect(WarpI18n::resolve($text, 'fr'))->toBe('Bonjour');
});

it('falls back to en when locale is missing from array', function () {
    $text = ['en' => 'Hello', 'de' => 'Hallo'];

    expect(WarpI18n::resolve($text, 'fr'))->toBe('Hello');
    expect(WarpI18n::resolve($text, 'ja'))->toBe('Hello');
});

it('falls back to first available value when en is also missing', function () {
    $text = ['de' => 'Hallo', 'fr' => 'Bonjour'];

    expect(WarpI18n::resolve($text, 'es'))->toBe('Hallo');
});

it('returns null for null input', function () {
    expect(WarpI18n::resolve(null, 'en'))->toBeNull();
});

it('returns null for empty string', function () {
    expect(WarpI18n::resolve('', 'en'))->toBeNull();
});

it('returns null for empty array', function () {
    expect(WarpI18n::resolve([], 'en'))->toBeNull();
});

it('resolveOrEmpty returns empty string instead of null', function () {
    expect(WarpI18n::resolveOrEmpty(null, 'en'))->toBe('');
    expect(WarpI18n::resolveOrEmpty('', 'en'))->toBe('');
    expect(WarpI18n::resolveOrEmpty([], 'en'))->toBe('');
});

it('resolveOrEmpty returns resolved value when available', function () {
    expect(WarpI18n::resolveOrEmpty('Hello', 'en'))->toBe('Hello');
    expect(WarpI18n::resolveOrEmpty(['en' => 'Hello', 'de' => 'Hallo'], 'de'))->toBe('Hallo');
});
