# language: fr

@feature/entity/changes
Fonctionnalité: Obtenir la différence entre 2 entités

Scénario: Différence entre 2 entités
    Quand je veux connaître les différences entre "old.json" et "new.json"
    Alors le résultat devrait ressembler au JSON suivant :
    """
    [
        "* Changement du maybe 'i came on too strong' => 'i waited too long'",
        "* Suppression du suhsuh 'nono'",
        "* Ajout du maybe i played 'my cards wrong'"
    ]
    """

Scénario: Différence entre 2 entités en excluant certains champs
    Quand je veux connaître les différences entre "old.json" et "new.json" en excluant "exception.json"
    Alors le résultat devrait ressembler au JSON suivant :
    """
    [
        "* Changement du maybe 'i came on too strong' => 'i waited too long'",
        "* Ajout du maybe i played 'my cards wrong'"
    ]
    """

Scénario: Différence entre 2 entités en utilisant une traduction des champs
    Quand je veux connaître les différences entre "old.json" et "new.json" avec les traductions "translation.json"
    Alors le résultat devrait ressembler au JSON suivant :
    """
    [
        "* Changement de peut-être 'i came on too strong' => 'i waited too long'",
        "* Suppression de sisi et pas nono 'nono'",
        "* Ajout de peut-être j'ai joué 'my cards wrong'"
    ]
    """
