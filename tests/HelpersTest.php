<?php

namespace Nerdcel\LanguageRelease\Tests;

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Content\Content;

class HelpersTest extends TestCase
{
    // ── autoIncludeButton() ──────────────────────────────────

    public function testAutoIncludeButtonDefaultsToTrue(): void
    {
        $this->assertTrue(autoIncludeButton());
    }

    public function testAutoIncludeButtonRespectsOption(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
            ],
            'options' => [
                'whoops' => false,
                'nerdcel.languagerelease' => [
                    'autoIncludeButton' => false,
                ],
            ],
        ]);

        $this->assertFalse(autoIncludeButton());
    }

    // ── languageReleaseFieldName() ───────────────────────────

    public function testLanguageReleaseFieldNameDefault(): void
    {
        $this->assertSame('languageReleased', languageReleaseFieldName());
    }

    public function testLanguageReleaseFieldNameCustom(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
            ],
            'options' => [
                'whoops' => false,
                'nerdcel.languagerelease' => [
                    'fieldName' => 'isPublished',
                ],
            ],
        ]);

        $this->assertSame('isPublished', languageReleaseFieldName());
    }

    // ── isLanguageReleased() with Page ───────────────────────

    public function testPageIsReleasedWhenFieldIsTrue(): void
    {
        $page = $this->createPage([
            'slug'    => 'released-page',
            'content' => [
                'title'            => 'Released Page',
                'languagereleased' => 'true',
            ],
        ]);

        $this->assertTrue(isLanguageReleased($page));
    }

    public function testPageIsNotReleasedWhenFieldIsFalse(): void
    {
        $page = $this->createPage([
            'slug'    => 'unreleased-page',
            'content' => [
                'title'            => 'Unreleased Page',
                'languagereleased' => 'false',
            ],
        ]);

        $this->assertFalse(isLanguageReleased($page));
    }

    public function testPageIsNotReleasedWhenFieldMissing(): void
    {
        $page = $this->createPage([
            'slug'    => 'no-field-page',
            'content' => [
                'title' => 'No Field Page',
            ],
        ]);

        $this->assertFalse(isLanguageReleased($page));
    }

    public function testPageIsNotReleasedWhenFieldIsEmpty(): void
    {
        $page = $this->createPage([
            'slug'    => 'empty-field-page',
            'content' => [
                'title'            => 'Empty Field Page',
                'languagereleased' => '',
            ],
        ]);

        $this->assertFalse(isLanguageReleased($page));
    }

    // ── isLanguageReleased() with Content ────────────────────

    public function testContentIsReleasedWhenFieldIsTrue(): void
    {
        $page = $this->createPage([
            'slug'    => 'content-released',
            'content' => [
                'languagereleased' => 'true',
            ],
        ]);

        $content = $page->content();
        $this->assertTrue(isLanguageReleased($content));
    }

    public function testContentIsNotReleasedWhenFieldIsFalse(): void
    {
        $page = $this->createPage([
            'slug'    => 'content-unreleased',
            'content' => [
                'languagereleased' => 'false',
            ],
        ]);

        $content = $page->content();
        $this->assertFalse(isLanguageReleased($content));
    }

    public function testContentIsNotReleasedWhenFieldMissing(): void
    {
        $page = $this->createPage([
            'slug'    => 'content-no-field',
            'content' => [
                'title' => 'Test',
            ],
        ]);

        $content = $page->content();
        $this->assertFalse(isLanguageReleased($content));
    }

    // ── isLanguageReleased() with custom field name ──────────

    public function testCustomFieldNameForReleasedCheck(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'content'  => static::$tmpDir . '/content',
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
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
            'slug'    => 'custom-field-page',
            'content' => [
                'ispublished' => 'true',
            ],
        ]);

        $this->assertTrue(isLanguageReleased($page));
    }

    public function testCustomFieldNameReturnsFalseForOldFieldName(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'content'  => static::$tmpDir . '/content',
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
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

        // Only has the default field name, not the configured one
        $page = $this->createPage([
            'slug'    => 'wrong-field-page',
            'content' => [
                'languagereleased' => 'true',
            ],
        ]);

        $this->assertFalse(isLanguageReleased($page));
    }

    // ── isLanguageReleased() bypasses check for Panel/API routes ──

    public function testIsLanguageReleasedReturnsTrueOnPanelRoute(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'content'  => static::$tmpDir . '/content',
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
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
            ],
            'users' => [
                [
                    'email' => 'test@example.com',
                    'role'  => 'admin',
                ],
            ],
            'request' => [
                'url' => 'https://example.com/panel/pages/test',
            ],
        ]);

        $this->app->impersonate('test@example.com');

        $page = $this->createPage([
            'slug'    => 'unreleased-panel-route',
            'content' => [
                'title'            => 'Unreleased Page',
                'languagereleased' => 'false',
            ],
        ]);

        $this->assertTrue(isLanguageReleased($page));
    }

    public function testIsLanguageReleasedReturnsTrueOnApiRoute(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'content'  => static::$tmpDir . '/content',
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
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
            ],
            'users' => [
                [
                    'email' => 'test@example.com',
                    'role'  => 'admin',
                ],
            ],
            'request' => [
                'url' => 'https://example.com/api/pages/test',
            ],
        ]);

        $this->app->impersonate('test@example.com');

        $page = $this->createPage([
            'slug'    => 'unreleased-api-route',
            'content' => [
                'title'            => 'Unreleased Page',
                'languagereleased' => 'false',
            ],
        ]);

        $this->assertTrue(isLanguageReleased($page));
    }

    public function testIsLanguageReleasedReturnsFalseForLoggedInUserOnFrontendRoute(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'content'  => static::$tmpDir . '/content',
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
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
            ],
            'users' => [
                [
                    'email' => 'test@example.com',
                    'role'  => 'admin',
                ],
            ],
            'request' => [
                'url' => 'https://example.com/en/some-page',
            ],
        ]);

        $this->app->impersonate('test@example.com');

        $page = $this->createPage([
            'slug'    => 'unreleased-frontend-route',
            'content' => [
                'title'            => 'Unreleased Page',
                'languagereleased' => 'false',
            ],
        ]);

        // Logged in but on a frontend route — should NOT bypass
        $this->assertFalse(isLanguageReleased($page));
    }

    public function testIsLanguageReleasedRespectsCustomPanelSlug(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'content'  => static::$tmpDir . '/content',
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
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
                'panel' => [
                    'slug' => 'admin',
                ],
            ],
            'users' => [
                [
                    'email' => 'test@example.com',
                    'role'  => 'admin',
                ],
            ],
            'request' => [
                'url' => 'https://example.com/admin/pages/test',
            ],
        ]);

        $this->app->impersonate('test@example.com');

        $page = $this->createPage([
            'slug'    => 'unreleased-custom-panel',
            'content' => [
                'title'            => 'Unreleased Page',
                'languagereleased' => 'false',
            ],
        ]);

        $this->assertTrue(isLanguageReleased($page));
    }

    // ── isAuthenticatedPreview() ─────────────────────────────

    public function testIsNotAuthenticatedPreviewWithoutPreviewParam(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
            ],
            'options' => [
                'whoops' => false,
            ],
            'request' => [
                'query' => [],
            ],
        ]);

        $this->assertFalse(isAuthenticatedPreview());
    }

    public function testIsNotAuthenticatedPreviewWithParamButNoUser(): void
    {
        $this->app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
            ],
            'options' => [
                'whoops' => false,
            ],
            'request' => [
                'query' => [
                    '_preview' => 'true',
                ],
            ],
        ]);

        $this->assertFalse(isAuthenticatedPreview());
    }
}

