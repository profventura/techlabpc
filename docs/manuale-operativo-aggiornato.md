# Manuale Operativo – Aggiornamento

## Introduzione
Questo documento aggiorna il manuale operativo con le ultime modifiche applicate all’app TechLab PC per migliorare velocità e usabilità.

## Accesso e Ruoli
- Autenticazione richiesta per tutte le sezioni (Accesso studenti/admin).
- Accesso consentito agli admin e ai leader di gruppo.
- Logout disponibile dal menu superiore.

## Navigazione
- Sidebar con collegamenti: Dashboard, Computer, Software, Studenti (solo admin), Gruppi, Docenti, Pagamenti (solo admin), Logs (solo admin).
- Breadcrumb dinamico in alto nella pagina.

## Dashboard
- Le card (PC totali, Pronti, In lavorazione, ecc.) si aggiornano in modo asincrono dopo il primo paint.
- Endpoint utilizzato: GET /api/view-cards?scope=dashboard
- In caso di assenza dati, il backend rigenera le metriche automaticamente.

## Liste e Tabelle (DataTables)
- Impostazioni performance:
  - deferRender: true (riduce il lavoro di creazione DOM)
  - autoWidth: false (evita calcoli di larghezza colonne)
  - order: [] (nessun ordinamento iniziale costoso)
  - pageLength: 10 (valore predefinito ottimizzato)
  - lengthMenu: [10, 25, 50, 100, 200, All]
- Filtri e ricerca:
  - Etichette accessibilità (aria-label) per campo di ricerca e selettore lunghezza.
- Esportazione CSV disponibile dove indicato (Laptop, Docenti, Studenti, Gruppi).

## Sezione Computer (Laptop)
- Filtri per stato, docente, gruppo.
- Creazione/modifica con valori di default configurabili.
- Associazione software (multi-selezione).
- Storico stati con registrazione dei cambi e log azioni.
- Regole di assegnazione: un docente può ricevere solo fino al numero richiesto.
- Import/Export CSV con colonne standard.
- Le card della pagina si aggiornano asincronamente via GET /api/view-cards?scope=laptops.

## Sezione Software
- Elenco software con nome, versione, licenza, notes, costo.
- CRUD completo.
- Le card si aggiornano via GET /api/view-cards?scope=software.

## Sezione Studenti (solo admin)
- Filtri: ruolo, attivo, gruppo, ricerca testuale.
- CRUD completo; password obbligatoria in creazione.
- Import CSV con gestione password: accetta password o password_hash; se assente imposta una password di default.
- Le card si aggiornano via GET /api/view-cards?scope=students.

## Sezione Gruppi
- Mostra gruppi, membri, laptop.
- Gestione leader: il gruppo deve avere almeno un leader; cambio leader aggiornando ruoli.
- Aggiunta/rimozione membri (controllo che uno studente non appartenga a più gruppi).
- Le card si aggiornano via GET /api/view-cards?scope=groups.

## Sezione Docenti
- Elenco con conteggi aggregati:
  - PC assegnati
  - PC pagati (somme verificati)
- CRUD completo, Import/Export CSV.
- Le card si aggiornano via GET /api/view-cards?scope=customers.

## Sezione Pagamenti (solo admin)
- Bonifici: importo, data, riferimento, numero PC pagati, stato, ricevuta (upload).
- Modifica e sostituzione ricevuta (elimina quella precedente se esiste).
- Le card si aggiornano via GET /api/view-cards?scope=payments.

## Logs (solo admin)
- Access logs e Action logs con DataTables.
- Pulsanti per svuotare singole tabelle log (con CSRF).

## Migrazioni e Indici
- Rotta /admin/migrate esegue le migrazioni SQL in database/migrations.
- Indici aggiunti:
  - group_members: role, group_id
  - students: role, active
- Migrazione: database/migrations/2026-01-03_add_indexes.sql

## Ottimizzazioni Frontend
- Rimosso preloader bloccante; contenuto appare subito.
- Icone (Iconify solar.json) caricate via fetch; rimosso preload per evitare warning del browser.
- CSS: rimosso @import per font; font caricati direttamente nel layout.

## Endpoint delle Card
- GET /api/view-cards?scope={dashboard|customers|students|groups|payments|laptops|software}
- Output: { scope, metrics: { metricName: value, … } }
- Se i dati mancano, il backend tenta un refresh dello scope richiesto.

## Sicurezza ed Affidabilità
- CSRF sui form mutanti.
- Autorizzazioni: restrizioni per azioni admin.
- Upload sicuri: nomi file sanificati e cartelle di destinazione controllate.
- Logging di accessi e azioni con riferimenti a attori e oggetti.

## Conversione in PDF
- È possibile convertire questo documento in PDF mediante:
  - Stampa in PDF dal browser
  - Utilizzo di strumenti come Pandoc o un editor markdown con esportazione PDF

