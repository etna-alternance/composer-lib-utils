# language: fr

@feature/login
Fonctionnalité: Générer des logins

Scénario: Générer un login
    Quand je veux générer un login avec le nom "Norris" et le prénom "Chuck"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le résultat devrait ressembler au JSON suivant :
    """
    "norris_c"
    """

Scénario: Générer un login avec des informations contenant des accents
    Quand je veux générer un login avec le nom "Àö'ŵŔ" et le prénom "Ŧoto"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le résultat devrait ressembler au JSON suivant :
    """
    "aowr_t"
    """

Plan du Scénario: Générer un login avec de mauvaises infos
    Quand je veux générer un login avec le nom "<lastname>" et le prénom "<firstname>"
    Alors il devrait y avoir eu une erreur
    Et    le message d'erreur devrait être "Firstname and lastname can't be empty to generate login" et le code 400

    Exemples:
        | lastname | firstname |
        | test     |           |
        |          | test      |
        |          |           |
