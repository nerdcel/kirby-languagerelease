<?php

namespace Nerdcel\LanguageRelease\Tests;

class TranslationsTest extends TestCase
{
    private array $expectedKeys = [
        'release-language',
        'unrelease-language',
        'dialog-release-text',
        'dialog-unrelease-text',
        'submit-release',
        'submit-unrelease',
        'cancel',
        'section-language',
        'section-code',
        'section-status',
        'section-default',
        'section-released',
        'section-unreleased',
        'section-translated',
        'section-missing',
        'tree-page',
    ];

    private array $languageCodes = ['de', 'en', 'es', 'fr', 'it', 'nl'];

    // ── Language files exist ─────────────────────────────────

    /**
     * @dataProvider languageProvider
     */
    public function testLanguageFileExists(string $lang): void
    {
        $file = dirname(__DIR__) . '/languages/' . $lang . '.php';
        $this->assertFileExists($file);
    }

    /**
     * @dataProvider languageProvider
     */
    public function testLanguageFileReturnsArray(string $lang): void
    {
        $file = dirname(__DIR__) . '/languages/' . $lang . '.php';
        $translations = require $file;

        $this->assertIsArray($translations);
        $this->assertNotEmpty($translations);
    }

    /**
     * @dataProvider languageProvider
     */
    public function testLanguageFileContainsAllKeys(string $lang): void
    {
        $file = dirname(__DIR__) . '/languages/' . $lang . '.php';
        $translations = require $file;

        foreach ($this->expectedKeys as $key) {
            $this->assertArrayHasKey(
                $key,
                $translations,
                "Language file '$lang' is missing key '$key'"
            );
        }
    }

    /**
     * @dataProvider languageProvider
     */
    public function testLanguageFileValuesAreStrings(string $lang): void
    {
        $file = dirname(__DIR__) . '/languages/' . $lang . '.php';
        $translations = require $file;

        foreach ($translations as $key => $value) {
            $this->assertIsString(
                $value,
                "Translation value for key '$key' in language '$lang' should be a string"
            );
            $this->assertNotEmpty(
                $value,
                "Translation value for key '$key' in language '$lang' should not be empty"
            );
        }
    }

    /**
     * @dataProvider languageProvider
     */
    public function testLanguageFileHasNoExtraKeys(string $lang): void
    {
        $file = dirname(__DIR__) . '/languages/' . $lang . '.php';
        $translations = require $file;
        $extraKeys = array_diff(array_keys($translations), $this->expectedKeys);

        $this->assertEmpty(
            $extraKeys,
            "Language file '$lang' has unexpected keys: " . implode(', ', $extraKeys)
        );
    }

    // ── Translation prefix loading ───────────────────────────

    public function testTranslationsArePrefixedCorrectly(): void
    {
        $translations = [];
        $languageFiles = $this->languageCodes;

        foreach ($languageFiles as $lang) {
            $file = dirname(__DIR__) . '/languages/' . $lang . '.php';
            if (file_exists($file)) {
                $langTranslations = require $file;
                $translations[$lang] = [];

                foreach ($langTranslations as $key => $value) {
                    $translations[$lang]['nerdcel.languagerelease.' . $key] = $value;
                }
            }
        }

        foreach ($this->languageCodes as $lang) {
            $this->assertArrayHasKey($lang, $translations);

            foreach ($this->expectedKeys as $key) {
                $prefixedKey = 'nerdcel.languagerelease.' . $key;
                $this->assertArrayHasKey(
                    $prefixedKey,
                    $translations[$lang],
                    "Prefixed translation key '$prefixedKey' missing for language '$lang'"
                );
            }
        }
    }

    // ── Dialog text placeholders ─────────────────────────────

    /**
     * @dataProvider languageProvider
     */
    public function testDialogTextsContainPlaceholders(string $lang): void
    {
        $file = dirname(__DIR__) . '/languages/' . $lang . '.php';
        $translations = require $file;

        $this->assertStringContainsString(
            '{language}',
            $translations['dialog-release-text'],
            "dialog-release-text in '$lang' should contain {language} placeholder"
        );
        $this->assertStringContainsString(
            '{page}',
            $translations['dialog-release-text'],
            "dialog-release-text in '$lang' should contain {page} placeholder"
        );
        $this->assertStringContainsString(
            '{language}',
            $translations['dialog-unrelease-text'],
            "dialog-unrelease-text in '$lang' should contain {language} placeholder"
        );
        $this->assertStringContainsString(
            '{page}',
            $translations['dialog-unrelease-text'],
            "dialog-unrelease-text in '$lang' should contain {page} placeholder"
        );
    }

    // ── Data providers ───────────────────────────────────────

    public static function languageProvider(): array
    {
        return [
            'German'  => ['de'],
            'English' => ['en'],
            'Spanish' => ['es'],
            'French'  => ['fr'],
            'Italian' => ['it'],
            'Dutch'   => ['nl'],
        ];
    }
}

