# Language Release - Frontend-Zugriffskontrolle
## Übersicht
Das Plugin kontrolliert den Zugriff auf nicht-freigegebene Sprachvarianten im Frontend. Im Kirby Panel bleiben alle Sprachvarianten zugänglich und bearbeitbar.
## Wie es funktioniert
### Frontend (Besucher)
- ❌ Nicht-freigegebene Sprachvarianten sind **nicht zugänglich**
- ✅ Freigegebene Sprachvarianten sind **normal zugänglich**
- ✅ Standard-Sprache ist **immer zugänglich**
### Panel (Redakteure)
- ✅ Alle Sprachvarianten sind **sichtbar und bearbeitbar**
- ✅ Preview-Button funktioniert für alle Sprachvarianten
- ✅ Keine Einschränkungen für Redakteure
### Preview-Modus (Authentifiziert)
- ✅ Mit gültigem Token sind nicht-released Seiten sichtbar
- ✅ URL-Parameter: `?_preview=true&_token=xxx` oder `?version=changes&_token=xxx`
- ✅ Token wird validiert - nur eingeloggte Nutzer können Preview sehen
- ❌ Ohne gültigen Token → normales Frontend-Verhalten
## Token-Validierung
Das Plugin prüft automatisch:
1. **URL-Parameter vorhanden?**
   - `_preview=true` ODER `version=changes`
   - `_token` Parameter
2. **Token gültig?**
   - Nutzer ist eingeloggt (Session aktiv)
   - Token ist valide
3. **Ergebnis:**
   - ✅ Alles valide → Preview wird angezeigt
   - ❌ Keine Parameter oder ungültiger Token → normale Zugriffskontrolle greift
## Preview-Button im Panel
Der Preview-Button im Kirby Panel funktioniert automatisch mit Token-Validierung. Nur eingeloggte Nutzer können nicht-released Seiten im Preview sehen.
### Sicherheit
- ✅ Token wird serverseitig validiert
- ✅ Nur eingeloggte Nutzer können Preview sehen
- ✅ Unauthentifizierte Nutzer sehen normales Verhalten (404/Redirect/Default)
- ✅ Keine Umgehung der Zugriffskontrolle möglich
