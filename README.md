[![Build Status](http://drone.etna-alternance.net/api/badge/github.com/etna-alternance/composer-lib-utils/status.svg?branch=master)](http://drone.etna-alternance.net/github.com/etna-alternance/composer-lib-utils)

# composer-lib-utils
Paquet composer contenant des fonctions utiles

Ce composant est un ensemble de classes pour centraliser certaines fonctionnalités.

## Le namespace

Toutes les classes `utils` de ce composant seront dans le namespace `ETNA\Utils`.

## Les classes

### CsvUtils

- `CsvUtils::getTokenFromArray` Réaligne un tableau mulitimensionnel PHP sur une seule dimension
- `CsvUtils::arrayToCsv` Convertis un tableau PHP en string CSV

### FileUtils

- `FileUtils::handleFile` Récupère le contenu d'un fichier texte (csv ou json) et le transforme en tableau PHP

### NotifyUtils

- `NotifyUtils::sendPrint` Envoie un job à sprinter pour l'impression de documents
- `NotifyUtils::sendMail` Envoie un job pigeon voyageur pour l'envoi de mail
