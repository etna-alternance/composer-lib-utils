# language: fr
Fonctionnalité: J'utilise le service Login

Scénario: Générer un vrai login
    Quand       je fais un GET sur "/login?firstname=alain&lastname=miskin"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "login": "miskin_a"
    }
    """

Scénario: Générer un login sans les infos
    Quand       je fais un GET sur /login
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    "Firstname and lastname can't be empty to generate login"
    """

Scénario: Générer un login avec des infos accentuées
    Quand       je fais un GET sur "/login?firstname=géràrd&lastname=sçhöûma"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "login": "schoum_g"
    }
    """

Scénario: Générer un login avec des espaces dans le nom
    Quand       je fais un GET sur "/login?firstname=jean-michel&lastname=le pot-e"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "login": "lepot-_j"
    }
    """
