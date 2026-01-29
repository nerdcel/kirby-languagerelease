<?php

use Nerdcel\LanguageRelease\Buttons\ReleaseButton;

load([
    ReleaseButton::class => 'classes/ReleaseButton.php',
], __DIR__);

// Load helpers
require_once __DIR__.'/helpers.php';

// Load translations with prefix
$translations = [];
$languageFiles = ['de', 'en', 'es', 'fr', 'it', 'nl'];

foreach ($languageFiles as $lang) {
    $file = __DIR__.'/languages/'.$lang.'.php';
    if (file_exists($file)) {
        $langTranslations = require $file;
        $translations[$lang] = [];

        foreach ($langTranslations as $key => $value) {
            $translations[$lang]['nerdcel.languagerelease.'.$key] = $value;
        }
    }
}

Kirby::plugin('nerdcel/languagerelease', [
    'version' => '1.0.0',
    'options' => [
        'autoIncludeButton' => true,
        'fieldName' => 'languageReleased',
        'behavior' => '404', // Options: '404', 'redirect', 'default-content'
    ],
    'translations' => $translations,
    'hooks' => [
        'system.loadPlugins:after' => function () {
            if (!autoIncludeButton()) {
                return;
            }

            // Set all default page view buttons
            $defaultButtons = kirby()->core()->area('site')['buttons'];

            // Gett all keys thats start with 'page.' or dont have a prefix
            $pageButtons = array_map(function ($entry) {
                return str_replace('page.', '', $entry);
            }, array_keys(array_filter($defaultButtons, function ($key) {
                return str_starts_with($key, 'page.') || ! str_contains($key, '.');
            }, ARRAY_FILTER_USE_KEY)));

            $pageButtons = array_filter($pageButtons, function ($button) {
                return $button !== 'versions'; // Exclude versions button to avoid conflicts
            });

            kirby()->extend([
                'options' => [
                    'panel.viewButtons.page' => [
                        ...$pageButtons,
                        'languagerelease',
                    ],
                ],
            ]);
        },
        'page.render:after' => function (string $contentType, array $data, string $html, Kirby\Cms\Page $page) {
            $kirby = kirby();

            // Skip check in Panel or authenticated preview
            if (isAuthenticatedPreview() || $kirby->request()->path()->startsWith('panel')) {
                return null;
            }

            // Check if current language is not default and page is not released
            $currentLang = $kirby->language();
            $defaultLang = $kirby->defaultLanguage();

            if ($currentLang && $defaultLang && ! isLanguageReleased($page) && $currentLang->code() !== $defaultLang->code()) {
                $behavior = $kirby->option('nerdcel.languagerelease.behavior', '404');

                switch ($behavior) {
                    case 'redirect':
                        // Redirect to default language
                        go($page->url($defaultLang->code()), 302);
                        exit;

                    case 'default-content':
                        // Show content from default language but keep URL
                        return $kirby->site()->visit($page->id(), $defaultLang->code())->render();

                    case '404':
                    default:
                        // Show 404 page
                        return $kirby->site()->errorPage()->render();
                }
            }

            return null;
        },
    ],
    'areas' => [
        'languagerelease' => [
            'buttons' => [
                'languagerelease' => fn(Kirby\Cms\Page $page) => new ReleaseButton($page),
            ],
            'dialogs' => [
                require __DIR__.'/dialogs/release-language-variant.php',
            ],
        ],
    ],
]);
