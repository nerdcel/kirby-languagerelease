<?php

namespace Nerdcel\LanguageRelease\Tests;

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Filesystem\Dir;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected App $app;
    protected static string $tmpDir;

    public static function setUpBeforeClass(): void
    {
        static::$tmpDir = sys_get_temp_dir() . '/kirby-languagerelease-tests-' . uniqid();
        Dir::make(static::$tmpDir);
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(static::$tmpDir) && is_dir(static::$tmpDir)) {
            Dir::remove(static::$tmpDir);
        }
    }

    protected function setUp(): void
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
                [
                    'code'    => 'fr',
                    'name'    => 'Français',
                ],
            ],
            'options' => [
                'whoops' => false,
                'nerdcel.languagerelease' => [
                    'autoIncludeButton' => true,
                    'fieldName'         => 'languageReleased',
                    'behavior'          => '404',
                ],
            ],
        ]);

        // Ensure helpers are loaded
        require_once dirname(__DIR__) . '/helpers.php';
    }

    protected function tearDown(): void
    {
        App::destroy();
    }

    /**
     * Create a page with given content for testing
     */
    protected function createPage(array $props = []): Page
    {
        return new Page(array_merge([
            'slug' => 'test-page',
        ], $props));
    }
}

