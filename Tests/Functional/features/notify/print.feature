# language: fr

@feature/print
Fonctionnalité: Envoyer des impressions a SPrinter

Scénario: Envoyer une impression
    Quand je lance une impression avec le template "document_1.docx" et les données contenues dans "document_1.json"
    Alors il ne devrait pas y avoir eu une erreur
    Et    il doit y avoir un message dans la file "sprinter.lefran_f" avec le corps contenu dans "simple_print_job.json"

Scénario: Envoyer une impression de plusieurs feuilles
    Quand je lance une impression avec le template "document_1.docx" et les données contenues dans "document_2.json"
    Alors il ne devrait pas y avoir eu une erreur
    Et    il doit y avoir un message dans la file "sprinter.lefran_f" avec le corps contenu dans "multiple_print_job.json"

Scénario: Envoyer une impression sur une queue spécifique
    Quand je lance une impression avec le template "document_1.docx" et les données contenues dans "document_1.json" dans la queue "sprinter.norris_c"
    Alors il ne devrait pas y avoir eu une erreur
    Et    il doit y avoir un message dans la file "sprinter.norris_c" avec le corps contenu dans "simple_print_job.json"

Scénario: Envoyer une impression de plusieurs feuilles
    Quand je lance une impression avec le template "document_1.docx" et les données contenues dans "document_2.json" dans la queue "sprinter.norris_c"
    Alors il ne devrait pas y avoir eu une erreur
    Et    il doit y avoir un message dans la file "sprinter.norris_c" avec le corps contenu dans "multiple_print_job.json"

Scénario: Envoyer une impression sans données
    Quand je lance une impression avec le template "document_1.docx" et les données contenues dans "empty_document.json"
    Alors il devrait y avoir eu une erreur
    Et    le message d'erreur devrait être "Bad data provided for printing" et le code 400

