<?php

use Carbon\Carbon;
use Vleap\Warps\Warp;
use Vleap\Warps\WarpAction;
use Vleap\Warps\WarpBuilder;
use Vleap\Warps\WarpMeta;
use Vleap\Warps\Transformers\WarpTransformer;
use Vleap\Warps\Transformers\Actions\ActionTransformer;

it('transforms a warp with string values', function () {
    $warp = (new WarpBuilder)
        ->setProtocol('warp:0.1.0')
        ->setName('test name')
        ->setTitle('test title')
        ->setDescription('test description')
        ->setPreview('https://abc.com/preview.png')
        ->addAction($action = WarpAction::create('test action')->link('https://example.com'))
        ->build();

    $actual = WarpTransformer::toArray($warp);

    expect($actual)->toBe([
        'protocol' => 'warp:0.1.0',
        'name' => 'test name',
        'title' => 'test title',
        'description' => 'test description',
        'preview' => 'https://abc.com/preview.png',
        'actions' => [
            ActionTransformer::toArray($action),
        ],
    ]);
});

it('transforms a warp with multilingual values', function () {
    $warp = (new WarpBuilder)
        ->setProtocol('warp:0.1.0')
        ->setName('test name')
        ->setTitle(['en' => 'test title', 'de' => 'test titel'])
        ->setDescription(['en' => 'test description', 'de' => 'test beschreibung'])
        ->setPreview('https://abc.com/preview.png')
        ->addAction($action = WarpAction::create('test action')->link('https://example.com'))
        ->build();

    $actual = WarpTransformer::toArray($warp);

    expect($actual)->toBe([
        'protocol' => 'warp:0.1.0',
        'name' => 'test name',
        'title' => ['en' => 'test title', 'de' => 'test titel'],
        'description' => ['en' => 'test description', 'de' => 'test beschreibung'],
        'preview' => 'https://abc.com/preview.png',
        'actions' => [
            ActionTransformer::toArray($action),
        ],
    ]);
});

it('transforms a warp with meta', function () {
    $createdAt = Carbon::parse('2024-01-15T10:30:00Z');
    $meta = new WarpMeta(
        chain: 'multiversx',
        identifier: 'test-identifier',
        query: 'test-query',
        hash: 'test-hash',
        creator: 'erd1test',
        createdAt: $createdAt,
    );

    $warp = new Warp(
        protocol: 'warp:0.1.0',
        name: 'test name',
        title: 'test title',
        description: 'test description',
        preview: 'https://abc.com/preview.png',
        actions: collect(),
        meta: $meta,
    );

    $actual = WarpTransformer::toArray($warp);

    expect($actual)->toHaveKey('meta');
    expect($actual['meta'])->toBe([
        'chain' => 'multiversx',
        'identifier' => 'test-identifier',
        'query' => 'test-query',
        'hash' => 'test-hash',
        'creator' => 'erd1test',
        'createdAt' => $createdAt->toIso8601String(),
    ]);
});

it('transforms a warp with meta without query', function () {
    $createdAt = Carbon::parse('2024-01-15T10:30:00Z');
    $meta = new WarpMeta(
        chain: 'multiversx',
        identifier: 'test-identifier',
        query: null,
        hash: 'test-hash',
        creator: 'erd1test',
        createdAt: $createdAt,
    );

    $warp = new Warp(
        protocol: 'warp:0.1.0',
        name: 'test name',
        title: 'test title',
        description: null,
        preview: 'https://abc.com/preview.png',
        actions: collect(),
        meta: $meta,
    );

    $actual = WarpTransformer::toArray($warp);

    expect($actual['meta']['query'])->toBeNull();
});

it('creates a warp from array with meta', function () {
    $createdAtString = '2024-01-15T10:30:00+00:00';
    $data = [
        'protocol' => 'warp:0.1.0',
        'name' => 'test name',
        'title' => 'test title',
        'description' => 'test description',
        'preview' => 'https://abc.com/preview.png',
        'actions' => [],
        'meta' => [
            'chain' => 'multiversx',
            'identifier' => 'test-identifier',
            'query' => 'test-query',
            'hash' => 'test-hash',
            'creator' => 'erd1test',
            'createdAt' => $createdAtString,
        ],
    ];

    $warp = WarpTransformer::fromArray($data);

    expect($warp->meta)->toBeInstanceOf(WarpMeta::class);
    expect($warp->meta->chain)->toBe('multiversx');
    expect($warp->meta->identifier)->toBe('test-identifier');
    expect($warp->meta->query)->toBe('test-query');
    expect($warp->meta->hash)->toBe('test-hash');
    expect($warp->meta->creator)->toBe('erd1test');
    expect($warp->meta->createdAt)->toBeInstanceOf(Carbon::class);
    expect($warp->meta->createdAt->toIso8601String())->toBe($createdAtString);
});

it('creates a warp from array with meta without query', function () {
    $createdAtString = '2024-01-15T10:30:00+00:00';
    $data = [
        'protocol' => 'warp:0.1.0',
        'name' => 'test name',
        'title' => 'test title',
        'description' => null,
        'preview' => 'https://abc.com/preview.png',
        'actions' => [],
        'meta' => [
            'chain' => 'multiversx',
            'identifier' => 'test-identifier',
            'query' => null,
            'hash' => 'test-hash',
            'creator' => 'erd1test',
            'createdAt' => $createdAtString,
        ],
    ];

    $warp = WarpTransformer::fromArray($data);

    expect($warp->meta->query)->toBeNull();
});

it('creates a warp from array with meta without creator', function () {
    $data = [
        'protocol' => 'warp:0.1.0',
        'name' => 'test name',
        'title' => 'test title',
        'description' => null,
        'preview' => null,
        'actions' => [],
        'meta' => [
            'chain' => 'multiversx',
            'identifier' => 'test-identifier',
            'query' => null,
            'hash' => 'test-hash',
            'createdAt' => '2024-01-15T10:30:00+00:00',
        ],
    ];

    $warp = WarpTransformer::fromArray($data);

    expect($warp->meta->creator)->toBe('');
});

it('creates a warp from array without meta', function () {
    $data = [
        'protocol' => 'warp:0.1.0',
        'name' => 'test name',
        'title' => 'test title',
        'description' => 'test description',
        'preview' => 'https://abc.com/preview.png',
        'actions' => [],
    ];

    $warp = WarpTransformer::fromArray($data);

    expect($warp->meta)->toBeNull();
});

it('round-trips warp with meta', function () {
    $createdAt = Carbon::parse('2024-01-15T10:30:00Z');
    $originalMeta = new WarpMeta(
        chain: 'multiversx',
        identifier: 'test-identifier',
        query: 'test-query',
        hash: 'test-hash',
        creator: 'erd1test',
        createdAt: $createdAt,
    );

    $original = new Warp(
        protocol: 'warp:0.1.0',
        name: 'test name',
        title: 'test title',
        description: 'test description',
        preview: 'https://abc.com/preview.png',
        actions: collect(),
        meta: $originalMeta,
    );

    $array = WarpTransformer::toArray($original);
    $restored = WarpTransformer::fromArray($array);

    expect($restored->meta)->toBeInstanceOf(WarpMeta::class);
    expect($restored->meta->chain)->toBe($original->meta->chain);
    expect($restored->meta->identifier)->toBe($original->meta->identifier);
    expect($restored->meta->query)->toBe($original->meta->query);
    expect($restored->meta->hash)->toBe($original->meta->hash);
    expect($restored->meta->creator)->toBe($original->meta->creator);
    expect($restored->meta->createdAt->equalTo($original->meta->createdAt))->toBeTrue();
});
