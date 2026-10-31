# Mes Médias

Repérez les médias les plus lourds et le poids de leurs variantes locales.

- Fiche, téléchargement et empreinte SHA-256 : https://mesplugins.fr/plugins/mes-medias/
- Toutes les extensions MesPlugins.fr : https://mesplugins.fr/plugins/

## Ce que fait l’extension

Depuis Outils > Mes Médias, analysez la bibliothèque par lots de 50 puis consultez les fichiers par poids décroissant. Aucun fichier n’est modifié ni supprimé.

## Limites

Les médias distants ou manquants restent inconnus. Les variantes manquantes donnent une mesure partielle. Réindexez après changement des fichiers. Pas de compression ou détection des médias inutilisés.

Première version réservée aux installations WordPress simples : l’activation est refusée en multisite.

## Installation

1. Téléchargez le ZIP depuis https://mesplugins.fr/plugins/mes-medias/ et vérifiez son empreinte.
2. Dans WordPress : Extensions > Ajouter > Téléverser une extension.
3. Activez puis ouvrez Outils > Mes Médias.

## Mises à jour

L’administration, le cron et WP-CLI consultent un manifeste fixe de mesplugins.fr, sans identifiant du site. Définir `MESPLUGINS_DISABLE_UPDATE_CHECK` à `true` dans wp-config.php désactive cette vérification. Aucune télémétrie.

## Version

0.1.0 — testée sur WordPress 7.1.4 / PHP 8.3.6. Ce dépôt contient exactement le ZIP publié (mes-medias-0.1.0.zip, SHA-256 `63e66179b14b72f0fe8386172ce4e90481f0134af74ceec8adcd142e9f569d00`).

Signaler un problème : les issues de ce dépôt, ou https://mesplugins.fr/contact/

Licence : GPL v2 ou ultérieure.
