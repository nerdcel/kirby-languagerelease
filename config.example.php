<?php

/**
 * Example Kirby Configuration for Language Release Plugin
 *
 * Add this to your site/config/config.php
 */

return [

    // Language Release Plugin Configuration
    // =========================================

    // Option 1: Using nested array (recommended)
    'nerdcel.languagerelease' => [
        // Define the field name used to store the release status
        'fieldName' => 'languageReleased', // Default value
    ],

    // Option 2: Using dot notation
    // 'nerdcel.languagerelease.fieldName' => 'languageReleased',


    // Example: Custom field name
    // -------------------------
    // 'nerdcel.languagerelease' => [
    //     'fieldName' => 'isPublished',
    // ],


    // Example: Environment-specific field names
    // ------------------------------------------
    // 'nerdcel.languagerelease.fieldName' => match (option('environment')) {
    //     'staging' => 'stagingRelease',
    //     'production' => 'productionRelease',
    //     default => 'languageReleased',
    // },

];

