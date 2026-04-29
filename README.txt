FACTURATION PHP - DEPLOIEMENT LOCAL
===================================

1) Prerequis
------------
- PHP 8.1+ installe et accessible en ligne de commande
- Navigateur web (Chrome, Edge, Firefox)
- (Optionnel) Webcam pour le scan code-barres

Verifier PHP:
    php -v


2) Recuperer le projet
----------------------
Si le projet est deja present, passer a l'etape 3.

Sinon:
    git clone <url-du-repo>
    cd tp_php


3) Structure importante
-----------------------
- Application: facturation/
- Entree principale: facturation/index.php
- Donnees:
  - facturation/data/utilisateurs.json
  - facturation/data/produits.json
  - facturation/data/factures.json


4) Lancer en local
------------------
Depuis la racine du projet (dossier tp_php), lancer:

    php -S localhost:8000

Puis ouvrir dans le navigateur:
    http://localhost:8000/facturation/auth/login.php


5) Comptes de demonstration
---------------------------
- Super Administrateur:
  - username: admin
  - password: admin123

- Manager:
  - username: manager
  - password: manager123

- Caissier:
  - username: caisse
  - password: caisse123


6) Verification rapide du fonctionnement
----------------------------------------
1. Se connecter avec un compte.
2. Ouvrir la caisse.
3. Saisir/scanner un code-barres existant.
4. Ajouter une quantite puis valider la facture.
5. Verifier:
   - mise a jour du stock dans produits.json
   - facture enregistree dans factures.json
   - calcul HT / TVA / TTC / net a payer visible


7) Camera / scanner (si utilise)
--------------------------------
- Autoriser l'acces camera dans le navigateur.
- Utiliser de preference http://localhost.
- En cas d'echec camera, la saisie manuelle du code reste disponible.


8) Depannage
------------
- "php n'est pas reconnu":
  -> Ajouter PHP au PATH systeme.

- Erreur de droits d'ecriture JSON:
  -> Verifier les permissions du dossier facturation/data.

- Port 8000 deja occupe:
  -> Lancer un autre port:
       php -S localhost:8080
     puis ouvrir:
       http://localhost:8080/facturation/auth/login.php


9) Arret du serveur
-------------------
Dans le terminal du serveur:
    Ctrl + C

