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
     * @param \Kirby\Cms\Page|\Kirby\Content\Content $model
     * @return bool
     */
    function isLanguageReleased(\Kirby\Cms\Page | \Kirby\Content\Content $model): bool
    {
        // In the Panel/API context, always treat as released
        $kirby = kirby();
        if ($kirby->user()) {
            $panelSlug = $kirby->option('panel.slug', 'panel');
            $path = $kirby->request()->path()->toString();

            // Check if we're on a Panel or API route
            if (
                str_starts_with($path, $panelSlug . '/') ||
                $path === $panelSlug ||
                str_starts_with($path, 'api/') ||
                $path === 'api'
            ) {
                return true;
            }
        }

        $fieldName = languageReleaseFieldName();

        if ($model instanceof \Kirby\Content\Content) {
            return $model->get($fieldName)->or(false)->toBool();
        }

        if ($model instanceof \Kirby\Cms\Page) {
            $content = $model->version('latest')->content(kirby()->language());
            return $content->get($fieldName)->or(false)->toBool();
        }

        return false;
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

