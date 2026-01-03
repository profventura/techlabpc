# Specifiche Tecniche – Aggiornamento

## Panoramica
Aggiornamento delle specifiche tecniche dell’app TechLab PC con le ottimizzazioni e le nuove funzionalità introdotte.

## Architettura
- Core:
  - DB: connessione PDO condivisa con errori come eccezioni, fetch associativo.
  - Router: registrazione rotte GET/POST, risoluzione parametri dinamici {id}.
  - Auth: login/logout, controllo ruoli (admin/leader), log accessi.
  - Helpers: URL, redirect, rendering view, upload file, flash messages.
- MVC semplificato:
  - Controllers coordinano modelli e viste.
  - Models eseguono query e logiche di business.
  - Views renderizzano la UI (Bootstrap + DataTables).

## Database
- Tabelle principali: students, customers, laptops, work_groups, group_members, software, payment_transfers, access_logs, action_logs, laptop_state_history.
- Indici aggiunti per performance:
  - group_members(role), group_members(group_id)
  - students(role), students(active)
- Migrazioni:
  - database/migrations/2026-01-03_add_indexes.sql
- Aggregazioni per card: tabella view_cards (metriche per scope).

## Endpoint Card
- Rotta: GET /api/view-cards?scope=…
- Scopes supportati: dashboard, customers, students, groups, payments, laptops, software.
- Sicurezza: richiede utente autenticato.
- Comportamento:
  - Se non risultano dati per lo scope, il servizio tenta un refresh specifico (es. ViewCardService::refreshDashboard()) e ripete la lettura.
- Output:
  - JSON: { scope: string, metrics: { metricName: number, … } }

## Modelli – Aggiornamenti
- Customer::all():
  - Conteggi via LEFT JOIN su subquery aggregate (laptops_count, pcs_paid_total) evitando subquery per riga.
- WorkGroup::all():
  - Conteggi membri e laptop via LEFT JOIN su subquery aggregate.
- PaymentTransfer, Laptop, Student, Software:
  - Metodi CRUD e utility; commentati in italiano per studio didattico.

## Controllers – Comportamenti
- LaptopController:
  - Filtri lista, storico stati, log azione, import/export CSV.
  - Aggiornamento card multiplo: Dashboard, Customers, Groups, Laptops.
- CustomerController:
  - Aggregazioni lato model, import/export CSV.
- StudentController:
  - Solo admin; import CSV con gestione password/password_hash.
- WorkGroupController:
  - Gestione leader e membri; vincoli su appartenenza a un solo gruppo.
- PaymentController:
  - Upload ricevute; sostituzione sicura; aggiornamento card Payments/Customers.
- LogsController:
  - Accessi e azioni con cancellazione tabelle; CSRF.
- SoftwareController:
  - CRUD e aggiornamento card software.

## Views – Performance
- DataTables:
  - deferRender: true, autoWidth: false, order: [], pageLength: 10.
  - Migliore TTI e draw time ridotto su tabelle grandi.
- Card asincrone:
  - Uso della classe metric-value e attributo data-metric per aggiornamento puntuale.
  - Fetch su /api/view-cards con mappatura semplice delle metriche.
- Layout:
  - Preloader rimosso (commentato) per ridurre blocchi visivi.
  - Iconify: caricato via fetch, evitato preload per compatibilità credenziali.
  - CSS: rimosso @import; font caricati via <link>.

## Sicurezza
- CSRF su tutte le azioni POST che modificano dati.
- Ruoli e autorizzazioni:
  - Admin per operazioni sensibili (studenti, pagamenti, logs).
  - Leader di gruppo ammesso al login ma con funzionalità limitate.
- Upload:
  - Nomi file sanificati; percorso di upload controllato; sostituzione ricevuta gestita.
- Log:
  - access_logs per login/logout; action_logs per azioni applicative.

## Prestazioni
- Query aggregate via JOIN/GROUP BY al posto di subquery per riga.
- Indici su colonne di filtro e aggregazione.
- Ottimizzazioni DataTables incidono sul rendering client-side.
- Possibilità futura: attivare server-side processing su DataTables per dataset enormi.

## Convenzioni e Autoload
- Namespace App\… con PSR-4 autoload via semplice funzione register.
- Controllers mappati in public/index.php con router minimalista.
- Views richiamate da Helpers::view includendo layout.php.

## Processo Migrazioni
- Avvio migrazioni: GET /admin/migrate (solo admin).
- Esecuzione sequenziale dei file SQL in database/migrations.
- Dopo migrazione: refresh delle card via ViewCardService::refreshAll().

## Limitazioni e Note
- Le card dipendono dall’aggiornamento di view_cards; in assenza dati si tenta un refresh automatico.
- Il preload delle icone è stato rimosso per evitare warning; il fetch è sufficiente.
- La generazione PDF dei manuali non è automatizzata; usare stampa in PDF dal browser o strumenti esterni.

## Allegati e Conversione in PDF
- Questo documento può essere convertito in PDF mediante:
  - Stampa in PDF dal browser
  - Strumenti come Pandoc o editor markdown con esportazione PDF

