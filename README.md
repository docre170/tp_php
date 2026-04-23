# SYSTÈME DE FACTURATION D'UN SUPERMARCHE - Faculté de Sciences Informatiques
Année académique 2025-2026

-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
un tp
# 1.1 Mise en situation
Un petit super marché souhaite moderniser son processus de vente. Actuellement, les
caissiers saisissent manuellement les articles, ce qui génère des erreurs et ralentit les transactions. Le gérant souhaite mettre en place un système de caisse informatisé, accessible
depuis un navigateur web, capable de lire des codes-barres via la caméra d’un téléphone
ou d’un ordinateur, et de produire des factures détaillées.
Vous êtes mandatés pour développer ce système en PHP procédural, sans recours à
aucun système de gestion de base de données.
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# 1.2 Objectifs pédagogiques

À l’issue de ce projet, l’étudiant sera capable de :
- Concevoir et structurer une application web PHP selon le paradigme procédural
- Utiliser les fichiers comme mécanisme de persistance des données
- Implémenter un système d’authentification et de contrôle d’accès basé sur les rôles
- Intégrer une bibliothèque de lecture de codes-barres dans une interface web
- Appliquer les bonnes pratiques de validation et d’assainissement des données en PHP
- Rédiger un rapport technique structuré en LaTeX
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# 1.3 Contrainte fondamentale

Toute la persistance des données doit être assurée exclusivement par des fichiers
au format que vous allez définir. L’utilisation d’un système de gestion de base de données
(MySQL, PostgreSQL, SQLite, MongoDB, etc.) est strictement interdite et entraînera
la note de zéro pour la partie concernée.
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# 1.4. Arborescence
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
# 1.5. Fonctions

config/ Paramètres globaux (taux TVA, chemins des fichiers, etc.)
auth/ Gestion de l’authentification et des sessions
modules/ Modules fonctionnels (produits, facturation, administration)
data/ Fichiers de persistance des données
includes/ Fonctions PHP réutilisables incluses dans les pages
assets/ Ressources statiques (CSS, JavaScript)
rapports/ Génération des rapports journaliers et mensuels

2. DESCRIPTION DU PROJET

Ce projet implémente un système de caisse informatisé pour un supermarché,
permettant :
- La lecture de codes-barres via la caméra
- L'enregistrement de produits
- La création et gestion de factures
- La gestion des comptes utilisateurs avec contrôle d'accès par rôles
- La génération de rapports journaliers et mensuels
- La persistance des données via des fichiers JSON
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
2. OUTILS

- Serveur web
- PHP
- Navigateur web moderne
- Accés à la camera
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


