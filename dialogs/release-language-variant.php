<?php

use Kirby\Toolkit\I18n;

return [
    'pattern' => 'languagerelease/(:any)/(:any)',
    'load' => function (string $id, string $language) {
        $page = page('page://'.$id, $language);
        $kirby = kirby();
        $fieldName = languageReleaseFieldName();

        $isReleased = $page?->content()->has($fieldName)
            ? $page->content()->get($fieldName)->or(0)->value()
            : 0;

        $textKey = $isReleased ? 'nerdcel.languagerelease.dialog-unrelease-text' : 'nerdcel.languagerelease.dialog-release-text';
        $text = I18n::template($textKey, null, [
            'language' => $language,
            'page' => $page?->title()->value(),
        ]);

        return [
            'component' => 'k-text-dialog',
            'props' => [
                'text' => $text,
                'submitButton' => [
                    'theme' => $isReleased ? 'love' : 'positive',
                    'label' => $isReleased ? I18n::translate('nerdcel.languagerelease.submit-unrelease') : I18n::translate('nerdcel.languagerelease.submit-release'),
                ],
                'cancelButton' => [
                    'label' => I18n::translate('nerdcel.languagerelease.cancel'),
                ],
            ],
        ];
    },
    'submit' => function (string $id, string $language) {
        $page = page('page://'.$id, $language);
        $fieldName = languageReleaseFieldName();

        $isReleased = $page?->content()->has($fieldName)
            ? $page->content()->get($fieldName)->or(false)->toBool()
            : false;

        $page?->update([$fieldName => $isReleased ? false : true]);

        return true;
    },
];
