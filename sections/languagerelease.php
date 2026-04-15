<?php

use Kirby\Toolkit\I18n;

return [
    'mixins' => [
        'headline',
    ],
    'computed' => [
        'languages' => function () {
            $kirby     = $this->kirby();
            $model     = $this->model();
            $languages = $kirby->languages();
            $fieldName = languageReleaseFieldName();
            $default   = $kirby->defaultLanguage();
            $result    = [];

            foreach ($languages as $language) {
                $code      = $language->code();
                $isDefault = $default && $code === $default->code();

                if ($isDefault) {
                    $released = true;
                } else {
                    $content  = $model->version('latest')->content($language);
                    $released = $content->get($fieldName)->or(false)->toBool();
                }

                $result[] = [
                    'code'      => $code,
                    'name'      => $language->name(),
                    'isDefault' => $isDefault,
                    'released'  => $released,
                ];
            }

            return $result;
        },
    ],
    'toArray' => function () {
        return [
            'label'     => $this->headline,
            'languages' => $this->languages,
        ];
    },
];

