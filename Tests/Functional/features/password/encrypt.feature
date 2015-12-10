# language: fr

@feature/password/encrypt
Fonctionnalité: Chiffrer un mot de passe

Scénario: Chiffrer un mot de passe
    Quand je chiffre le mot de passe "m6N2JGQu"
    Alors le résultat devrait ressembler au JSON suivant :
    """
    "#^\\$2a\\$07\\$.{53}$#"
    """
