# language: fr

@feature/csv
Fonctionnalité: Transformer un tableau PHP en chaîne de caractères CSV

Scénario: Transformer un simple tableau
    Quand je convertis en csv le tableau contenu dans "simple_array.json"
    Alors il ne devrait pas y'avoir eu une erreur
    Et    le résultat devrait être identique au fichier csv "csv_from_simple_array.csv"
    Et    le csv sortant devrait contenir 1 lignes

Scénario: Transformer un tableau contenant plusieurs lignes
    Quand je convertis en csv le tableau contenu dans "multilines_array.json"
    Alors il ne devrait pas y'avoir eu une erreur
    Et    le résultat devrait être identique au fichier csv "csv_from_mutlilines_array.csv"
    Et    le csv sortant devrait contenir 4 lignes

Scénario: Transformer un tableau multidimensionnel
    Quand je convertis en csv le tableau contenu dans "multidimentional_array.json"
    Alors il ne devrait pas y'avoir eu une erreur
    Et    le résultat devrait être identique au fichier csv "csv_from_multidimentional_array.csv"
    Et    le csv sortant devrait contenir 3 lignes

Scénario: Transformer un tableau multidimensionnel en prefixant les champs
    Quand je convertis en csv le tableau contenu dans "multidimentional_array.json" en prefixant avec "prefix"
    Alors il ne devrait pas y'avoir eu une erreur
    Et    le résultat devrait être identique au fichier csv "prefixed_csv_from_multidimentional_array.csv"
    Et    le csv sortant devrait contenir 3 lignes

Plan du Scénario: Transformer un tableau mal formatté
    Quand je convertis en csv le tableau contenu dans "<array_source>"
    Alors il devrait y'avoir eu une erreur
    Et    le message d'erreur devrait être "Bad csv" et le code 400

    Exemples:
        | array_source                   |
        | no_matching_column_number.json |
        | no_matching_column_name.json   |
