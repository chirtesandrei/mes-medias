=== Mes Médias ===
Contributors: chirtesandrei
Requires at least: 6.4
Tested up to: 7.1.4
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Repérez les médias les plus lourds et le poids de leurs variantes locales.

== Description ==
Depuis Outils > Mes Médias, analysez la bibliothèque par lots de 50 puis consultez les fichiers par poids décroissant. Aucun fichier n’est modifié ni supprimé.

Bêta publique gratuite, documentée en français. Installation WordPress simple ; multisite non pris en charge.

== Limites ==
Les médias distants ou manquants restent inconnus. Les variantes manquantes donnent une mesure partielle. Réindexez après changement des fichiers. Pas de compression ou détection des médias inutilisés.

== Données et mises à jour ==
Aucune télémétrie, aucun compte, aucun appel sur les pages publiques. L'administration, le cron et WP-CLI consultent au plus une fois toutes les douze heures un manifeste fixe sur https://mesplugins.fr/api/catalogue/mes-medias/mise-a-jour.json ; aucun paramètre, adresse de site ou identifiant n'est envoyé par le plugin. Le serveur reçoit les informations réseau nécessaires à une requête HTTPS. L'installation d'une mise à jour utilise le mécanisme natif de WordPress.
Définir MESPLUGINS_DISABLE_UPDATE_CHECK à true dans wp-config.php désactive ces contrôles. Le transient propre au plugin est supprimé à la désinstallation.

== Installation ==
1. Conserver une sauvegarde et essayer d'abord sur un site de test.
2. Extensions > Ajouter > Téléverser une extension : choisir le ZIP.
3. Activer et ouvrir Outils > Mes Médias.

== Changelog ==
= 0.1.0 =
Première bêta. Installation, fonctionnalités, droits d'accès, désactivation et désinstallation contrôlés sur WordPress 7.1.4 et PHP 8.3.6.
