<?php

namespace Nerdcel\LanguageRelease\Tests;

class TreeSectionTest extends TestCase
{
    // ── Section file structure ────────────────────────────────

    public function testTreeSectionFileExists(): void
    {
        $this->assertFileExists(
            dirname(__DIR__) . '/sections/languagerelease-tree.php'
        );
    }

    public function testTreeSectionFileReturnsArray(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease-tree.php';

        $this->assertIsArray($section);
    }

    public function testTreeSectionHasMixins(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease-tree.php';

        $this->assertArrayHasKey('mixins', $section);
        $this->assertContains('headline', $section['mixins']);
    }

    public function testTreeSectionHasComputedLanguages(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease-tree.php';

        $this->assertArrayHasKey('computed', $section);
        $this->assertArrayHasKey('languages', $section['computed']);
        $this->assertIsCallable($section['computed']['languages']);
    }

    public function testTreeSectionHasComputedPages(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease-tree.php';

        $this->assertArrayHasKey('computed', $section);
        $this->assertArrayHasKey('pages', $section['computed']);
        $this->assertIsCallable($section['computed']['pages']);
    }

    public function testTreeSectionHasToArray(): void
    {
        $section = require dirname(__DIR__) . '/sections/languagerelease-tree.php';

        $this->assertArrayHasKey('toArray', $section);
        $this->assertIsCallable($section['toArray']);
    }
}

