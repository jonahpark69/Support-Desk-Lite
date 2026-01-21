# Demo script (3-5 minutes)
Objectif: montrer un workflow complet user/agent/admin avec un focus sur securite, UX et export.

## Duree cible
4 minutes

## Prerequis
- App lancee: `php artisan serve` + `npm run dev`
- DB seed: `php artisan migrate --seed`
- Comptes:
  - agent@demo.test / password
  - user@demo.test / password
  - admin: cree via Tinker si besoin
- Mailer en log: `MAIL_MAILER=log`
- Ouvrir 2 sessions navigateur (user + agent)

## Checklist avant demo
- [ ] Migrations OK
- [ ] Seed demo OK
- [ ] Storage link OK si besoin: `php artisan storage:link`
- [ ] `storage/logs/laravel.log` accessible pour montrer les emails
- [ ] Page Tickets, Ticket Show, Profil et Admin Agents ouvertes

## Scenario pas a pas

### 1) Login user -> creation ticket
**Action**
- Se connecter en user.
- Creer un ticket (titre, priorite, categorie).

**Ce que je dis**
- "L'utilisateur cree un ticket en quelques champs, avec priorite et categorie."
- "Le systeme valide et affiche un toast de confirmation."

**Benefice**
- Traite rapide et feedback immediat.

### 2) Vue index + filtres + badges FR + empty states
**Action**
- Aller sur Tickets, tester une recherche vide puis reset.

**Ce que je dis**
- "Les badges sont normalises en FR pour statuts et priorites."
- "Les etats vides sont clairs et proposent une action utile."

**Benefice**
- Lisibilite, UX propre, evite les ecrans vides.

### 3) Login agent -> prise en charge + statut + timeline
**Action**
- Se connecter en agent.
- Ouvrir le ticket, cliquer "Prendre en charge", changer le statut.

**Ce que je dis**
- "L'agent prend en charge et passe le ticket en cours."
- "La timeline conserve l'historique des statuts."

**Benefice**
- Traçabilite et suivi clair.

### 4) Ajouter commentaire -> notif email + preferences
**Action**
- Ajouter un commentaire.
- Montrer `storage/logs/laravel.log` pour l'email.
- Aller au profil et desactiver la notif "Nouveaux commentaires".

**Ce que je dis**
- "Les emails partent aux bonnes personnes, pas au commentateur."
- "Les preferences sont gerables par l'utilisateur."

**Benefice**
- Moins de spam, controle utilisateur.

### 5) Export CSV
**Action**
- Depuis l'index, cliquer "Exporter CSV".

**Ce que je dis**
- "Export compatible Excel avec BOM UTF-8, respecte les filtres."

**Benefice**
- Partage et reporting rapides.

### 6) Page admin agents
**Action**
- Se connecter en admin.
- Ouvrir /admin/agents.

**Ce que je dis**
- "Vue admin securisee, liste des agents et charge par tickets."

**Benefice**
- Pilotage simple de la capacite support.
