# language: fr

@feature/file
Fonctionnalité: Récupérer un fichier et transformer son contenu en ressource

Plan du Scénario: Récupérer le contenu d'un fichier contenant du csv
    Quand je veux récupérer le contenu du fichier "<csv_file>"
    Alors il ne devrait pas y'avoir eu une erreur
    Et    le résultat devrait être identique au fichier "simple_csv_parsed.json"

    Exemples:
        | csv_file       |
        | simple_csv     |
        | simple_csv.csv |
        | simple_csv.txt |

Plan du Scénario: Récupérer le contenu d'un fichier contenant du JSON
    Quand je veux récupérer le contenu du fichier "<json_file>"
    Alors il ne devrait pas y'avoir eu une erreur
    Et    le résultat devrait être identique au fichier "simple_json_parsed.json"

    Exemples:
        | json_file        |
        | simple_json      |
        | simple_json.json |
        | simple_json.txt  |

Scénario: Récuperer le contenu d'un fichier csv mal formatté
    Quand je veux récupérer le contenu du fichier "bad_csv.csv"
    Alors il devrait y'avoir eu une erreur
    Et    le message d'erreur devrait être "Bad CSV given" et le code 400
