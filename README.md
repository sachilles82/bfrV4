# Projekt [bfr - reportix]

Eine kurze Beschreibung, was Ihre Laravel Livewire Applikation macht.
(z.B.: Eine skalierbare Anwendung zur Verwaltung von Mitarbeitern und Unternehmensdaten.)

---

## Inhaltsverzeichnis

- [Anforderungen](#anforderungen)
- [Installation](#installation)
- [Konfiguration](#konfiguration)
- [Datenbank-Setup](#datenbank-setup)
- [Entwicklungsserver](#entwicklungsserver)
- [Tests](#tests)
- [Wichtige Technologien](#wichtige-technologien)
- [Performance-Analyse & Optimierungen](#performance-analyse--optimierungen)
- [Lizenz](#lizenz)

---

## Anforderungen

- PHP >= 8.2
- Composer 2.x
- Node.js >= 18.x & NPM
- MySQL >= 8.0
- Redis Server
- [Laravel Herd](https://herd.laravel.com/) (Empfohlen für lokale Entwicklung unter macOS/Windows) oder alternative lokale Entwicklungsumgebung (Docker, Valet, etc.)

---

## Installation

1.  **Repository klonen:**
    ```bash
    git clone [https://de.wikipedia.org/wiki/Repository](https://de.wikipedia.org/wiki/Repository)
    cd [projekt-verzeichnis]
    ```
2.  **Composer-Abhängigkeiten installieren:**
    ```bash
    composer install
    ```
3.  **NPM-Abhängigkeiten installieren:**
    ```bash
    npm install
    ```
4.  **Frontend-Assets kompilieren:**
    ```bash
    npm run build
    # Oder für die Entwicklung:
    # npm run dev
    ```
5.  **`.env`-Datei erstellen:**
    ```bash
    cp .env.example .env
    ```
6.  **App-Schlüssel generieren:**
    ```bash
    php artisan key:generate
    ```

---

## Konfiguration

Öffnen Sie die `.env`-Datei und konfigurieren Sie mindestens die folgenden Variablen:

- **Datenbank:**
  ```dotenv
  DB_CONNECTION=mysql
  DB_HOST=bfr.test
  DB_PORT=3306
  DB_DATABASE=ihre_datenbank
  DB_USERNAME=ihr_benutzer
  DB_PASSWORD=ihr_passwort

- **Performance**
  Dokumentation: Performance-Analyse der EmployeeTable-Abfrage
  Datum: 01.05.2025

Komponente: app/Livewire/Alem/Employee/EmployeeTable.php

Kontext:
Bei der Anzeige der Mitarbeiterliste im EmployeeTable-Component wurde ein signifikanter Performance-Unterschied zwischen verschiedenen Teams festgestellt. Team 1 (ca. 10.000 Einträge) zeigte ursprünglich Abfragezeiten von ca. 46ms für die Hauptabfrage, während Team 2 (ca. 6.500 Einträge) nur ca. 2ms benötigte.

Analyse durchgeführte Schritte:

Query-Identifikation: Die Hauptabfrage zur Ermittlung der Mitarbeiterliste wurde identifiziert. Sie beinhaltet Joins zwischen users, employees, team_user, professions, stages und departments mit Filterung nach users.user_type, users.model_status, users.deleted_at, team_user.team_id und Sortierung nach users.created_at DESC mit LIMIT 8.
Index-Überprüfung: Alle relevanten Migrationen wurden geprüft. Es wurde sichergestellt, dass alle für die WHERE-Klauseln, ORDER BY und JOIN-Operationen notwendigen Spalten indiziert sind:
users: idx_users_filter_sort (user_type, model_status, deleted_at, created_at), idx_company_department (company_id, department_id)
team_user: unique(team_id, user_id), index(user_id), idx_team_user_team_user (team_id, user_id), idx_team_user_user_team (user_id, team_id)
employees: index(user_id), index(profession_id), index(stage_id)
professions, stages, departments: Primary Key Index auf id.
FORCE INDEX-Tests:
Ohne FORCE INDEX: Die Performance verschlechterte sich dramatisch (175ms für Team 1, 143ms für Team 2). Der MySQL-Optimizer wählte ohne den Hinweis einen ineffizienten Ausführungsplan, trotz vorhandener Indizes auf allen Tabellen.
Mit FORCE INDEX (idx_users_filter_sort): Die Performance kehrte zum (fast) ursprünglichen Niveau zurück (ca. 54ms für Team 1, ca. 2ms für Team 2).
EXPLAIN-Analyse (mit FORCE INDEX): Die Analyse zeigte für beide Teams einen identischen, effizienten Ausführungsplan. Der idx_users_filter_sort-Index wird genutzt ("Backward index scan"), und alle Joins erfolgen effizient über Indizes (eq_ref, ref). Entscheidend war die Schätzung der zu prüfenden users-Zeilen, die für beide Teams gleich war (ca. 8156).
Statistiken aktualisieren: ANALYZE TABLE wurde auf relevanten Tabellen ausgeführt, was jedoch keine signifikante Änderung bewirkte.
Ursache für den verbleibenden Performance-Unterschied (54ms vs. 2ms):

Da die Indizierung korrekt ist und der (erzwungene) Ausführungsplan identisch und effizient ist, liegt die Ursache eindeutig in der Datenverteilung (Data Distribution / Cardinality):

Die Datenbank schätzt zwar, dass sie ~8156 users-Indexeinträge prüfen muss, die den WHERE-Kriterien entsprechen.
Die tatsächliche Anzahl der Einträge, die gescannt werden müssen, bis 8 Benutzer gefunden werden, die auch zum jeweiligen Team gehören (via INNER JOIN team_user), unterscheidet sich aber stark.
Für Team 2 befinden sich die 8 passenden Mitarbeiter sehr weit "vorne" im Index-Scan (bezogen auf created_at DESC). Die LIMIT 8-Bedingung wird schnell erfüllt.
Für Team 1 müssen signifikant mehr Index-Einträge durchlaufen und mit team_user abgeglichen werden, bevor 8 Mitarbeiter gefunden werden, die alle Kriterien (inkl. team_id = 1) erfüllen. Dieser Mehraufwand beim Index-Scan und den Join-Prüfungen verursacht die höhere Latenz von ca. 54ms.
Entscheidung & Konsequenzen:

FORCE INDEX ist notwendig: Um eine akzeptable Performance zu gewährleisten, wird FORCE INDEX (idx_users_filter_sort) in der Abfrage der EmployeeTable-Komponente beibehalten. Dies stellt sicher, dass der effizienteste bekannte Plan verwendet wird.
Akzeptanz der Datenverteilung: Der verbleibende Unterschied wird als Resultat der natürlichen Datenverteilung akzeptiert. Die Performance von ~54ms für Team 1 wird als ausreichend betrachtet.
Indizes: Die Indizes auf team_user (hinzugefügt während der Analyse) und alle anderen relevanten Tabellen sind per Migration sichergestellt.
Zukünftige Optimierung (Optional):

Sollte die Performance für Team 1 (< 50ms) in Zukunft kritisch werden, ist die Implementierung von Anwendungs-Level-Caching (Redis) für die Ergebnisse dieser Abfrage die nächste empfohlene Maßnahme.



