# language: fr
Fonctionnalité: J'utilise le service Password

Scénario: Générer un mot de passe et sa version cryptée
    Quand       je fais un GET sur "/password"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "password": "#^[a-zA-Z0-9\\.]{8}$#",
        "encrypted": "#^\\$2a\\$07\\$[a-zA-Z0-9\\/\\.]{53}$#"
    }
    """
