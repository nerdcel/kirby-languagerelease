<?php

if (!function_exists('autoIncludeButton')) {
    /**
     * Check if the release button should be auto-included in page view buttons
     *
     * @return bool
     */
    function autoIncludeButton(): bool
    {
        return kirby()->option('nerdcel.languagerelease.autoIncludeButton', true);
    }

}

if (!function_exists('languageReleaseFieldName')) {
    /**
     * Get the configured field name for language release status
     *
     * @return string
     */
    function languageReleaseFieldName(): string
    {
        return kirby()->option('nerdcel.languagerelease.fieldName', 'languageReleased');
    }
}

if (!function_exists('isLanguageReleased')) {
    /**
     * Check if a page's language variant is released
     *
     * @param \Kirby\Cms\Page $page
     * @return bool
     */
    function isLanguageReleased(\Kirby\Cms\Page | \Kirby\Content\Content $model): bool
    {
        $fieldName = languageReleaseFieldName();

        if ($model instanceof \Kirby\Content\Content) {
            $content = $model;
        } elseif ($model instanceof \Kirby\Cms\Page) {
            $content = $model->content();
        } else {
            return false;
        }
        if (!$model->has($fieldName)) {
            return false;
        }

        return $content->get($fieldName)->or(false)->toBool();
    }
}

if (!function_exists('isAuthenticatedPreview')) {
    /**
     * Check if current request is an authenticated preview
     * Validates preview token and checks for preview parameters
     *
     * @return bool
     */
    function isAuthenticatedPreview(): bool
    {
        $kirby = kirby();
        $request = $kirby->request();

        // Check if preview parameters are present
        $hasPreviewParam = $request->get('_preview') === 'true';

        if (!$hasPreviewParam) {
            return false;
        }

        // Validate token if present
        if ($kirby->auth()?->user()?->isLoggedIn()) {
            return true;
        }

        return false;
    }
}

