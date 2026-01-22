# Changelog
All notable changes to this project will be documented in this file.
The format is based on Keep a Changelog and this project adheres to Semantic Versioning (SemVer).

## [Unreleased]
### Added
- N/A

### Changed
- N/A

### Fixed
- N/A

### Security
- N/A

## [1.0.0] - 2026-01-22
### Added
- Authentification via Laravel Breeze
- Roles user/agent/admin
- Tickets: creation, index, recherche, filtres, pagination, show
- Commentaires sur ticket
- Workflow agent: prise en charge en 1 clic + changement de statut
- Timeline (historique des statuts)
- Badges normalises + mapping FR (statuts/priorites)
- Etats vides + skeleton loader
- Export CSV des tickets (respect des filtres)
- Admin: page liste des agents
- Notifications: email "Nouveau commentaire"
- Preferences de notifications

### Changed
- Toasts coherents et unifies (success/error/info/warning) sur les actions cles

### Fixed
- Harmonisation des retours utilisateur (messages succes/erreur)

### Security
- Rate limiting sur la creation de tickets
- Validation + nettoyage commentaires (anti-XSS basique)
