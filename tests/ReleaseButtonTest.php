<?php

namespace Nerdcel\LanguageRelease\Tests;

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Nerdcel\LanguageRelease\Buttons\ReleaseButton;

class ReleaseButtonTest extends TestCase
{
    private function loadReleaseButtonClass(): void
    {
        require_once dirname(__DIR__) . '/classes/ReleaseButton.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadReleaseButtonClass();
    }

    // ── constructor ──────────────────────────────────────────

    public function testConstructorSetsComponentName(): void
    {
        $page = $this->createPage([
            'slug'    => 'button-test',
            'content' => [
                'title'            => 'Button Test',
                'languagereleased' => 'true',
            ],
        ]);

        $button = new ReleaseButton($page);

        $this->assertSame('k-languagerelease-view-button', $button->component);
    }

    // ── props() ──────────────────────────────────────────────

    public function testPropsContainsLanguages(): void
    {
        $page = $this->createPage([
            'slug'    => 'props-lang-test',
            'content' => [
                'title' => 'Test',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertArrayHasKey('languages', $props);
        $this->assertIsArray($props['languages']);
        $this->assertCount(3, $props['languages']);
    }

    public function testPropsContainsCurrentLanguage(): void
    {
        $page = $this->createPage([
            'slug'    => 'props-current-lang',
            'content' => [
                'title' => 'Test',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertArrayHasKey('currentLanguage', $props);
        $this->assertSame('en', $props['currentLanguage']);
    }

    public function testPropsContainsDefaultLanguage(): void
    {
        $page = $this->createPage([
            'slug'    => 'props-default-lang',
            'content' => [
                'title' => 'Test',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertArrayHasKey('defaultLanguage', $props);
        $this->assertSame('en', $props['defaultLanguage']);
    }

    public function testPropsReleasedIsTrueWhenFieldIsTrue(): void
    {
        $page = $this->createPage([
            'slug'    => 'released-props',
            'content' => [
                'title'            => 'Released',
                'languagereleased' => 'true',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertArrayHasKey('released', $props);
        $this->assertTrue($props['released']);
    }

    public function testPropsReleasedIsFalseWhenFieldIsFalse(): void
    {
        $page = $this->createPage([
            'slug'    => 'unreleased-props',
            'content' => [
                'title'            => 'Unreleased',
                'languagereleased' => 'false',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertArrayHasKey('released', $props);
        $this->assertFalse($props['released']);
    }

    public function testPropsReleasedIsFalseWhenFieldMissing(): void
    {
        $page = $this->createPage([
            'slug'    => 'missing-field-props',
            'content' => [
                'title' => 'No Release Field',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertFalse($props['released']);
    }

    public function testPropsContainsDialogPath(): void
    {
        $page = $this->createPage([
            'slug'    => 'dialog-props',
            'content' => [
                'title' => 'Dialog Test',
                'uuid'  => 'test-uuid-123',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertArrayHasKey('dialog', $props);
        $this->assertStringContainsString('languagerelease/', $props['dialog']);
        $this->assertStringEndsWith('/en', $props['dialog']);
    }

    // ── Custom field name ────────────────────────────────────

    public function testPropsWithCustomFieldName(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'content'  => static::$tmpDir . '/content',
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
                'cache'    => static::$tmpDir . '/cache',
            ],
            'languages' => [
                [
                    'code'    => 'en',
                    'name'    => 'English',
                    'default' => true,
                ],
                [
                    'code'    => 'de',
                    'name'    => 'Deutsch',
                ],
            ],
            'options' => [
                'whoops' => false,
                'nerdcel.languagerelease' => [
                    'fieldName' => 'isPublished',
                ],
            ],
        ]);

        $page = $this->createPage([
            'slug'    => 'custom-field-props',
            'content' => [
                'title'       => 'Custom Field',
                'ispublished' => 'true',
            ],
        ]);

        $button = new ReleaseButton($page);
        $props  = $button->props();

        $this->assertTrue($props['released']);
    }
}

