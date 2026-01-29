# Language Release Plugin - Konfiguration

## Plugin-Optionen

Das Plugin kann über die Kirby-Konfiguration angepasst werden.

### Feldname konfigurieren

Standardmäßig verwendet das Plugin das Feld `languageReleased` um den Release-Status zu speichern. Du kannst einen eigenen Feldnamen definieren:

#### In `site/config/config.php`:

```php
return [
    'nerdcel.languagerelease' => [
        'fieldName' => 'languageReleased', // Standard
    ],
];
```

#### Beispiel mit eigenem Feldnamen:

```php
return [
    'nerdcel.languagerelease' => [
        'fieldName' => 'isPublished', // Eigener Feldname
    ],
];
```

#### Für mehrere Umgebungen:

```php
return [
    'nerdcel.languagerelease.fieldName' => 'myCustomField',
];
```

## Blueprint-Konfiguration

Stelle sicher, dass deine Seiten-Blueprints das konfigurierte Feld enthalten:

### Mit Standard-Feldname (`languageReleased`):

```yaml
fields:
  languageReleased:
    type: toggle
    label: Language Released
    default: false
```

### Mit eigenem Feldnamen (z.B. `isPublished`):

```yaml
fields:
  isPublished:
    type: toggle
    label: Published
    default: false
```

## Helper-Funktionen

Das Plugin stellt zwei Helper-Funktionen bereit:

### `languageReleaseFieldName()`

Gibt den konfigurierten Feldnamen zurück:

```php
$fieldName = languageReleaseFieldName();
// Gibt 'languageReleased' oder deinen konfigurierten Namen zurück
```

### `isLanguageReleased($page)`

Prüft, ob eine Sprachvariante freigegeben ist:

```php
if (isLanguageReleased($page)) {
    echo 'This page is released';
}
```

## Verwendung in Templates

```php
// Nur freigegebene Seiten anzeigen
$pages = $site->children()->filter(function($page) {
    return isLanguageReleased($page);
});

// Feldname dynamisch abrufen
$fieldName = languageReleaseFieldName();
$isReleased = $page->content()->get($fieldName)->toBool();
```

## Mehrsprachigkeit

Das Plugin unterstützt folgende Sprachen:
- Deutsch (de)
- Englisch (en)
- Spanisch (es)
- Französisch (fr)
- Italienisch (it)
- Niederländisch (nl)

Alle Texte werden automatisch in der Panel-Sprache angezeigt.

## Beispiel-Konfigurationen

### Minimale Konfiguration (nutzt Standards):
```php
// Keine Konfiguration nötig - verwendet 'languageReleased'
```

### Erweiterte Konfiguration:
```php
return [
    'nerdcel.languagerelease' => [
        'fieldName' => 'contentReleased',
    ],
];
```

### Multi-Site Setup:
```php
return [
    'nerdcel.languagerelease.fieldName' => match (option('environment')) {
        'staging' => 'stagingRelease',
        'production' => 'productionRelease',
        default => 'languageReleased',
    },
];
```

