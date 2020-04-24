# language: fr
Fonctionnalité: J'utilise le service CSV

Scénario: Convertir des données en CSV
    Quand       je fais un POST sur /csv avec le corps contenu dans "csv/csv.json"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "csv": "id;name\n1;toto\n2;tutu\n3;titi"
    }
    """

Scénario: Convertir des données multi niveau en CSV en les aplanissant
    Quand       je fais un POST sur /csv avec le corps contenu dans "csv/csv_multi_level.json"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "csv": "id;name;data_color;data_count\n1;toto;red;1\n2;tutu;blue;4\n3;titi;green;2"
    }
    """

Scénario: Essayer de convertir des données vides
    Quand       je fais un POST sur /csv avec le corps contenu dans "csv/empty_csv.json"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "csv": ""
    }
    """

Scénario: Essayer de convertir des données n'ayant pas les mêmes headers
    Quand       je fais un POST sur /csv avec le corps contenu dans "csv/bad_headers_csv.json"
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    "Bad csv"
    """

Scénario: Utiliser sputcsv sans paramêtres customs
    Quand       je fais un GET sur "/sputcsv"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "csv": "42;\"ouais ouais\"\n"
    }
    """

Scénario: Utiliser sputcsv avec des paramêtres customs
    Quand       je fais un GET sur "/sputcsv?delimiter=,&enclosure='&eol=|"
    Alors       le status HTTP devrait être 200
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "csv": "42,'ouais ouais'|"
    }
    """
