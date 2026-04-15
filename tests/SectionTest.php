<?php

namespace Nerdcel\LanguageRelease\Tests;

class SectionTest extends TestCase
{
    // ── Section file structure ────────────────────────────────

    public function testSectionFileExists(): void
    {
        $this->assertFileExists(
            dirname(__DIR__) . '/sections/languagerelease.php'
        );
    }

    public function testSectionFileReturnsArray(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease.php';

        $this->assertIsArray($section);
    }

    public function testSectionHasMixins(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease.php';

        $this->assertArrayHasKey('mixins', $section);
        $this->assertContains('headline', $section['mixins']);
    }

    public function testSectionHasComputedLanguages(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease.php';

        $this->assertArrayHasKey('computed', $section);
        $this->assertArrayHasKey('languages', $section['computed']);
        $this->assertIsCallable($section['computed']['languages']);
    }

    public function testSectionHasToArray(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease.php';

        $this->assertArrayHasKey('toArray', $section);
        $this->assertIsCallable($section['toArray']);
    }
}

