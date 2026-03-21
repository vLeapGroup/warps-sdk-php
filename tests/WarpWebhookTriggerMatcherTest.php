<?php

use Vleap\Warps\WarpWebhookTriggerMatcher;

$highlightPayload = [
    'event_type' => 'readwise.highlight.created',
    'highlight' => [
        'text' => 'The key insight is that simplicity scales.',
        'note' => 'Apply this to API design',
        'book_title' => 'A Philosophy of Software Design',
    ],
];

$highlightTrigger = [
    'type' => 'webhook',
    'source' => 'readwise',
    'match' => ['event_type' => 'readwise.highlight.created'],
    'inputs' => [
        'TEXT' => 'highlight.text',
        'NOTE' => 'highlight.note',
        'BOOK' => 'highlight.book_title',
        'SOURCE' => 'readwise',
    ],
];

describe('WarpWebhookTriggerMatcher::matches', function () use ($highlightPayload, $highlightTrigger) {
    it('matches when the payload satisfies all conditions', function () use ($highlightPayload, $highlightTrigger) {
        expect(WarpWebhookTriggerMatcher::matches($highlightTrigger, $highlightPayload))->toBeTrue();
    });

    it('does not match when a condition value differs', function () use ($highlightTrigger) {
        $payload = ['event_type' => 'reader.document.finished'];
        expect(WarpWebhookTriggerMatcher::matches($highlightTrigger, $payload))->toBeFalse();
    });

    it('does not match when the condition field is missing from the payload', function () use ($highlightTrigger) {
        expect(WarpWebhookTriggerMatcher::matches($highlightTrigger, []))->toBeFalse();
    });

    it('matches when trigger has no match conditions', function () use ($highlightPayload) {
        $trigger = ['type' => 'webhook', 'source' => 'readwise', 'inputs' => []];
        expect(WarpWebhookTriggerMatcher::matches($trigger, $highlightPayload))->toBeTrue();
    });

    it('matches when match is an empty array', function () use ($highlightPayload) {
        $trigger = ['type' => 'webhook', 'source' => 'readwise', 'match' => [], 'inputs' => []];
        expect(WarpWebhookTriggerMatcher::matches($trigger, $highlightPayload))->toBeTrue();
    });

    it('matches numeric condition values', function () {
        $trigger = ['type' => 'webhook', 'source' => 'stripe', 'match' => ['data.amount' => 100]];
        expect(WarpWebhookTriggerMatcher::matches($trigger, ['data' => ['amount' => 100]]))->toBeTrue();
        expect(WarpWebhookTriggerMatcher::matches($trigger, ['data' => ['amount' => 200]]))->toBeFalse();
    });

    it('matches boolean condition values', function () {
        $trigger = ['type' => 'webhook', 'source' => 'custom', 'match' => ['active' => true]];
        expect(WarpWebhookTriggerMatcher::matches($trigger, ['active' => true]))->toBeTrue();
        expect(WarpWebhookTriggerMatcher::matches($trigger, ['active' => false]))->toBeFalse();
    });

    it('requires all conditions to pass', function () {
        $trigger = ['type' => 'webhook', 'source' => 'github', 'match' => ['action' => 'opened', 'pull_request.draft' => false]];
        expect(WarpWebhookTriggerMatcher::matches($trigger, ['action' => 'opened', 'pull_request' => ['draft' => false]]))->toBeTrue();
        expect(WarpWebhookTriggerMatcher::matches($trigger, ['action' => 'opened', 'pull_request' => ['draft' => true]]))->toBeFalse();
        expect(WarpWebhookTriggerMatcher::matches($trigger, ['action' => 'closed', 'pull_request' => ['draft' => false]]))->toBeFalse();
    });
});

describe('WarpWebhookTriggerMatcher::resolveInputs', function () use ($highlightPayload, $highlightTrigger) {
    it('resolves dot-path inputs from the payload', function () use ($highlightPayload, $highlightTrigger) {
        $result = WarpWebhookTriggerMatcher::resolveInputs($highlightTrigger, $highlightPayload);
        expect($result['TEXT'])->toBe('The key insight is that simplicity scales.');
        expect($result['NOTE'])->toBe('Apply this to API design');
        expect($result['BOOK'])->toBe('A Philosophy of Software Design');
    });

    it('returns static literals for values without dots', function () use ($highlightPayload, $highlightTrigger) {
        $result = WarpWebhookTriggerMatcher::resolveInputs($highlightTrigger, $highlightPayload);
        expect($result['SOURCE'])->toBe('readwise');
    });

    it('returns null for missing dot-paths', function () use ($highlightTrigger) {
        $result = WarpWebhookTriggerMatcher::resolveInputs($highlightTrigger, []);
        expect($result['TEXT'])->toBeNull();
    });

    it('returns empty array when trigger has no inputs', function () use ($highlightPayload) {
        $result = WarpWebhookTriggerMatcher::resolveInputs(['type' => 'webhook', 'source' => 'x'], $highlightPayload);
        expect($result)->toBe([]);
    });
});
