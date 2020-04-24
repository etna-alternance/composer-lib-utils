# language: fr
Fonctionnalité: J'utilise le service File

Scénario: Interpreter le contenu d'un fichier JSON
    Quand       j'upload les documents
        | name | file                    |
        | file | file/super_content.json |
    Et          je fais un POST sur /file
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "content": {
            "content": "Coucou toi !"
        }
    }
    """

Scénario: Interpreter le contenu d'un fichier CSV
    Quand       j'upload les documents
        | name | file                   |
        | file | file/super_content.csv |
    Et          je fais un POST sur /file
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "content": [
            {
                "id": "1",
                "name": "tata",
                "color": "red"
            },
            {
                "id": "2",
                "name": "tutu",
                "color": "blue"
            },
            {
                "id": "3",
                "name": "titi",
                "color": "green"
            },
            {
                "id": "4",
                "name": "toto",
                "color": "blouge"
            }
        ]
    }
    """

Scénario: Interpreter le contenu d'un fichier CSV cassé
    Quand       j'upload les documents
        | name | file                 |
        | file | file/bad_content.csv |
    Et          je fais un POST sur /file
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    "Bad CSV given"
    """
