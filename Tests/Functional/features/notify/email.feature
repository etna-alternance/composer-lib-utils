# language: fr

@feature/email
Fonctionnalité: Envoyer des emails

Scénario: Envoyer un email
    Quand j'envoie un mail a "test@test.test" avec "sender@send.er" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "simple_email_job.json"

Scénario: Envoyer un email avec des pieces jointes
    Quand j'envoie un mail a "test@test.test" avec "sender@send.er" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json" avec comme pièce jointe les fichiers "file1.txt;file2.txt"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "email_with_files_job.json"

Scénario: Envoyer un email avec certaines des pieces jointes qui n'existent pas
    Quand j'envoie un mail a "test@test.test" avec "sender@send.er" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json" avec comme pièce jointe les fichiers "file12.txt;file1.txt;file2.txt;file42.txt"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "email_with_files_job.json"

Scénario: Envoyer un email avec une copie de mail
    Quand j'envoie un mail a "test@test.test" avec "sender@send.er" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json" avec en copie les emails "jesuisuncc@bloub.com"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "simple_email_job_with_cc.json"

Scénario: Envoyer un email avec plusieur copie de mail
    Quand j'envoie un mail a "test@test.test" avec "sender@send.er" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json" avec en copie les emails "panda@bloub.com;salut@bloub.com;patapouf@bloub.com"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "simple_email_job_with_many_cc.json"

Scénario: Envoyer un email avec des pieces jointes et plusieurs cc
    Quand j'envoie un mail a "test@test.test" avec "sender@send.er" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json" avec comme pièce jointe les fichiers "file1.txt;file2.txt" avec en copie les emails "jesuis@bloub.com;unepetite@pouet.net;patate@saler.fr"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "email_with_files_job_and_cc.json"

Scénario: Envoyer un email avec une copie de mail qui est re@etna
    Quand j'envoie un mail a "test@test.test" avec "sender@send.er" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json" avec en copie les emails "re@etna-alternance.net"
    Alors il ne devrait pas y avoir eu une erreur
    Et    le producer "email" devrait avoir publié un message dans la queue "email" avec le corps contenu dans "simple_email_job.json"

Plan du Scénario: Envoyer un email sans donner d'adresse
    Quand j'envoie un mail a "<sender>" avec "<receiver>" avec le titre "title" et le template contenu dans le fichier "simple_template" et les tokens contenus dans "simple_tokens.json"
    Alors il devrait y avoir eu une erreur
    Et    le message d'erreur devrait être "No email provided" et le code 400

    Exemples:
        | sender         | receiver       |
        | test@test.test |                |
        |                | sender@send.er |
        |                |                |
