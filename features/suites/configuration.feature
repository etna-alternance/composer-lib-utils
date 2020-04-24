# language: fr
Fonctionnalité: J'instancie mon bundle puis le configure

Scénario: Utiliser le Utils Bundle
    Etant donné que je crée un nouveau kernel de test
    Quand       je configure le kernel avec le fichier "good_config.php"
    Et          je boot le kernel
    Alors       ca devrait s'être bien déroulé
    Et          le service "etna_utils.csv_service" devrait exister
    Et          le service "etna_utils.file_service" devrait exister
    Et          le service "etna_utils.login_service" devrait exister
    Et          le service "etna_utils.notify_service" devrait exister
    Et          le service "etna_utils.password_service" devrait exister
    Et          je n'ai plus besoin du kernel de test
