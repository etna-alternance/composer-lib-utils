# language: fr
Fonctionnalité: J'utilise le service Notify

Scénario: Envoyer un mail sans piece jointe
    Quand       je fais un POST sur /mail avec le corps contenu dans "mail/mail.json"
    Alors       le status HTTP devrait être 200
    Et          le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "simple_mail.json"

Scénario: Envoyer un mail avec des pieces jointes
    Quand       j'upload les documents
        | name | file                    |
        | json | file/super_content.json |
        | csv  | file/super_content.csv  |
    Et          je fais un POST sur /mail avec le corps contenu dans "mail/mail.json"
    Alors       le status HTTP devrait être 200
    Et          le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "mail_with_files.json"

Scénario: Envoyer un mail avec des pieces jointes et des tokens dans le contenu
    Quand       j'upload les documents
        | name | file                    |
        | json | file/super_content.json |
        | csv  | file/super_content.csv  |
    Et          je fais un POST sur /mail avec le corps contenu dans "mail/mail_tokens.json"
    Alors       le status HTTP devrait être 200
    Et          le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "mail_with_tokens.json"

Scénario: Envoyer un mail avec des infos manquantes
    Quand       j'upload les documents
        | name | file                    |
        | json | file/super_content.json |
        | csv  | file/super_content.csv  |
    Et          je fais un POST sur /mail avec le corps contenu dans "mail/bad_mail.json"
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    "No email provided"
    """

@Sprinter
Scénario: Envoyer une impression a sprinter
    Quand       j'upload les documents
        | name     | file                    |
        | template | file/super_content.json |
    Et          je fais un POST sur /print avec le corps contenu dans "print/print.json"
    Alors       le status HTTP devrait être 200
    Et          le producer "SPrinter" devrait avoir publié un message dans la queue "sprinter.blu" avec le corps contenu dans "simple_print.json"

@Sprinter
Scénario: Envoyer une impression a sprinter sans vouloir imprimer
    Quand       j'upload les documents
        | name     | file                    |
        | template | file/super_content.json |
    Et          je fais un POST sur /print avec le corps contenu dans "print/print_no_print.json"
    Alors       le status HTTP devrait être 200
    Et          le producer "SPrinter" devrait avoir publié un message dans la queue "sprinter.blu" avec le corps contenu dans "simple_no_print.json"

@Sprinter
Scénario: Envoyer une impression a sprinter sans préciser de routingKey
    Quand       j'upload les documents
        | name     | file                    |
        | template | file/super_content.json |
    Et          je fais un POST sur /print avec le corps contenu dans "print/print_no_routing_key.json"
    Alors       le status HTTP devrait être 200
    Et          le producer "SPrinter" devrait avoir publié un message dans la queue "sprinter.lefran_f" avec le corps contenu dans "simple_no_print.json"

@Sprinter
Scénario: Envoyer une impression a sprinter sans données
    Quand       j'upload les documents
        | name     | file                    |
        | template | file/super_content.json |
    Et          je fais un POST sur /print avec le corps contenu dans "print/print_no_data.json"
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    "Bad data provided for printing"
    """
