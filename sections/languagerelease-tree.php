<?php

use Kirby\Toolkit\I18n;

return [
    'mixins' => [
        'headline',
    ],
    'computed' => [
        'languages' => function () {
            $kirby     = $this->kirby();
            $languages = $kirby->languages();
            $default   = $kirby->defaultLanguage();
            $result    = [];

            foreach ($languages as $language) {
                $code = $language->code();
                $result[] = [
                    'code'      => $code,
                    'name'      => $language->name(),
                    'isDefault' => $default && $code === $default->code(),
                ];
            }

            return $result;
        },
        'pages' => function () {
            $kirby     = $this->kirby();
            $languages = $kirby->languages();
            $fieldName = languageReleaseFieldName();
            $default   = $kirby->defaultLanguage();

            // Build a flat list of all pages with depth info
            $flatList = [];

            $collect = function ($pages, int $depth = 0, string $parentId = '') use (&$collect, &$flatList, $languages, $fieldName, $default) {
                foreach ($pages as $page) {
                    $statuses = [];

                    foreach ($languages as $language) {
                        $code      = $language->code();
                        $isDefault = $default && $code === $default->code();

                        if ($isDefault) {
                            $released       = true;
                            $hasTranslation = true;
                        } else {
                            $content        = $page->version('latest')->content($language);
                            $released       = $content->get($fieldName)->or(false)->toBool();
                            $hasTranslation = $page->translation($code) !== null && $page->translation($code)->exists();
                        }

                        $statuses[$code] = [
                            'released'       => $released,
                            'isDefault'      => $isDefault,
                            'hasTranslation' => $hasTranslation,
                        ];
                    }

                    $children    = $page->childrenAndDrafts();
                    $hasChildren = $children->count() > 0;
                    $pageId      = $page->id();

                    $flatList[] = [
                        'id'          => $pageId,
                        'parentId'    => $parentId,
                        'title'       => $page->title()->value(),
                        'depth'       => $depth,
                        'hasChildren' => $hasChildren,
                        'url'         => $page->panel()->url(),
                        'statuses'    => $statuses,
                    ];

                    if ($hasChildren) {
                        $collect($children, $depth + 1, $pageId);
                    }
                }
            };

            $collect($kirby->site()->childrenAndDrafts());

            return $flatList;
        },
    ],
    'toArray' => function () {
        return [
            'label'     => $this->headline,
            'languages' => $this->languages,
            'pages'     => $this->pages,
        ];
    },
];

