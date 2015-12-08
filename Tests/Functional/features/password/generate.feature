# language: fr

@feature/password/generate
Fonctionnalité: Générer des mots de passe

Scénario: Générer un mot de passe
    Quand je génère un mot de passe
    Alors le mot de passe devrait contenir 6 lettres et 2 autres caractères

Scénario: Générer un mot de passe avec un nombre de lettres défini
    Quand je génère un mot de passe avec 8 lettres
    Alors le mot de passe devrait contenir 8 lettres et 2 autres caractères

Scénario: Générer un mot de passe avec un nombre de lettres défini et un nombre de caractères non alphanumérique
    Quand je génère un mot de passe avec 8 lettres et 3 autres caractères
    Alors le mot de passe devrait contenir 8 lettres et 3 autres caractères
