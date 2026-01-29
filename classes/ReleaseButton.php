<?php

namespace Nerdcel\LanguageRelease\Buttons;

use Kirby\Cms\App;
use Kirby\Cms\ModelWithContent;
use Kirby\Panel\Ui\Buttons\ViewButton;

class ReleaseButton extends ViewButton
{
    protected App $kirby;

    public function __construct(ModelWithContent $model)
    {
        $this->kirby = $model->kirby();

        parent::__construct(
            component: 'k-languagerelease-view-button',
            model: $model,
        );
    }

    public function props(): array
    {
        $fieldName = languageReleaseFieldName();
        $released = $this->model->content()->has($fieldName)
            ? $this->model->content()->get($fieldName)->or(false)->toBool()
            : false;

        return [
            'languages' => $this->kirby->languages()->toArray(),
            'currentLanguage' => $this->kirby->currentLanguage()?->code(),
            'defaultLanguage' => $this->kirby->defaultLanguage()?->code(),
            'released' => $released,
            'dialog' => 'languagerelease/' . $this->model->uuid() . '/' . $this->kirby->currentLanguage()?->code(),
        ];
    }
}
