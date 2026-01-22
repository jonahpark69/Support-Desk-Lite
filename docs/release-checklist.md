# Release checklist

## Avant release
- [ ] Lancer les tests: `php artisan test`
- [ ] Verifier la creation de ticket (user)
- [ ] Verifier la prise en charge agent (assignation en 1 clic)
- [ ] Verifier le changement de statut + timeline
- [ ] Verifier les commentaires (ajout + affichage)
- [ ] Verifier les notifications email "Nouveau commentaire"
- [ ] Verifier les preferences de notifications
- [ ] Verifier l'export CSV (respect des filtres)
- [ ] Verifier la page admin "Agents"
- [ ] Verifier README + captures d'ecran

## Tag & push
```bash
git checkout main
git merge release/v1.0.0
php artisan test
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin main
git push origin --tags
```

## Post-release (optionnel)
- [ ] Si develop continue, merger `main` vers `develop`
