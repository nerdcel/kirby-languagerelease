<?php

namespace Nerdcel\LanguageRelease\Tests;

use Kirby\Cms\App;

class PluginRegistrationTest extends TestCase
{
    // ── Version detection ────────────────────────────────────

    public function testComposerJsonIsValid(): void
    {
        $composerFile = dirname(__DIR__) . '/composer.json';
        $this->assertFileExists($composerFile);

        $composerData = json_decode(file_get_contents($composerFile), true);
        $this->assertIsArray($composerData);
        $this->assertArrayHasKey('name', $composerData);
        $this->assertSame('nerdcel/kirby-languagerelease', $composerData['name']);
        $this->assertArrayHasKey('type', $composerData);
        $this->assertSame('kirby-plugin', $composerData['type']);
    }

    public function testComposerJsonDoesNotHardcodeVersion(): void
    {
        $composerFile = dirname(__DIR__) . '/composer.json';
        $composerData = json_decode(file_get_contents($composerFile), true);

        // Version should come from git tags via Packagist, not from composer.json
        $this->assertArrayNotHasKey(
            'version',
            $composerData,
            'composer.json should not contain a hardcoded version – Packagist derives it from git tags'
        );
    }

    public function testInstalledVersionIsAvailable(): void
    {
        $version = \Composer\InstalledVersions::getPrettyVersion('nerdcel/kirby-languagerelease');
        $this->assertNotNull($version);
        $this->assertNotEmpty($version);
    }

    // ── Plugin options defaults ──────────────────────────────

    public function testDefaultAutoIncludeButtonOption(): void
    {
        $this->assertTrue(
            $this->app->option('nerdcel.languagerelease.autoIncludeButton')
        );
    }

    public function testDefaultFieldNameOption(): void
    {
        $this->assertSame(
            'languageReleased',
            $this->app->option('nerdcel.languagerelease.fieldName')
        );
    }

    public function testDefaultBehaviorOption(): void
    {
        $this->assertSame(
            '404',
            $this->app->option('nerdcel.languagerelease.behavior')
        );
    }

    // ── Plugin structure ─────────────────────────────────────

    public function testPluginFilesExist(): void
    {
        $basePath = dirname(__DIR__);

        $this->assertFileExists($basePath . '/index.php');
        $this->assertFileExists($basePath . '/helpers.php');
        $this->assertFileExists($basePath . '/classes/ReleaseButton.php');
        $this->assertFileExists($basePath . '/dialogs/release-language-variant.php');
        $this->assertFileExists($basePath . '/sections/languagerelease.php');
    }

    public function testDialogReturnsValidStructure(): void
    {
        $dialog = require dirname(__DIR__) . '/dialogs/release-language-variant.php';

        $this->assertIsArray($dialog);
        $this->assertArrayHasKey('pattern', $dialog);
        $this->assertArrayHasKey('load', $dialog);
        $this->assertArrayHasKey('submit', $dialog);
        $this->assertSame('languagerelease/(:any)/(:any)', $dialog['pattern']);
        $this->assertIsCallable($dialog['load']);
        $this->assertIsCallable($dialog['submit']);
    }

    // ── Behavior option values ───────────────────────────────

    /**
     * @dataProvider validBehaviorProvider
     */
    public function testBehaviorOptionAcceptsValidValues(string $behavior): void
    {
        $app = new App([
            'roots' => [
                'index'    => static::$tmpDir,
                'accounts' => static::$tmpDir . '/accounts',
                'sessions' => static::$tmpDir . '/sessions',
            ],
            'options' => [
                'whoops' => false,
                'nerdcel.languagerelease' => [
                    'behavior' => $behavior,
                ],
            ],
        ]);

        $this->assertSame(
            $behavior,
            $app->option('nerdcel.languagerelease.behavior')
        );
    }

    public static function validBehaviorProvider(): array
    {
        return [
            '404'             => ['404'],
            'redirect'        => ['redirect'],
            'default-content' => ['default-content'],
        ];
    }
}

