
# MOVIES_API


## Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- Docker
- Docker Compose

## Installation et Configuration

Suivez ces étapes pour configurer l'environnement de développement.

### Configurer Docker

1. Construisez les images Docker et lancez les conteneurs :

   ```bash
   docker-compose up -d
   ```

   Ceci va télécharger et construire les images nécessaires et démarrer les conteneurs.

### Configurer Symfony

1. Une fois les conteneurs Docker lancés, installez les dépendances de Symfony :

   ```bash
   docker-compose exec web composer install
   ```

2. Créez et migrez votre base de données :

   ```bash
   docker-compose exec web php bin/console doctrine:database:create
   docker-compose exec web php bin/console doctrine:migrations:migrate
   ```
   
3. Remplir la base de données :
    ```bash
    docker-compose exec web php bin/console doctrine:fixtures:load
    ```

## Utilisation

Pour accéder à l'application, ouvrez votre navigateur et allez à `http://localhost:8000`.

## Commandes Utiles

- Pour arrêter les conteneurs Docker :

  ```bash
  docker-compose down
  ```

- Pour entrer dans un conteneur Docker :

  ```bash
  docker-compose exec [nom-du-service] bash
  ```

- Pour consulter les logs :

  ```bash
  docker-compose logs [nom-du-service]
  ```

## Développement

Comming soon...

---

# Documentation API - Gestion de Films

## Introduction

L'API MOVIES_API supporte désormais les réponses en format JSON et XML. Les clients peuvent spécifier le format souhaité via l'en-tête Accept dans leur requête. Si aucun format n'est spécifié, l'API répondra par défaut en JSON.

## Endpoints

- Liste des films (GET /film/list) : Renvoie une liste de tous les films. Accepte application/json et application/xml.
- Détails d'un film (GET /film/{id}) : Renvoie les détails d'un film spécifique. Remplacez {id} par l'ID du film. Accepte application/json et application/xml.
- Création d'un film (POST /film) : Crée un nouveau film. Le corps de la requête doit être au format JSON.
- Mise à jour d'un film (PUT /film/{id}) : Met à jour un film spécifique. Remplacez {id} par l'ID du film. Le corps de la requête doit être au format JSON.
- Suppression d'un film (DELETE /film/{id}) : Supprime un film spécifique. Remplacez {id} par l'ID du film.

## Formats de Réponse

- JSON : Pour recevoir la réponse en JSON, incluez Accept: application/json dans l'en-tête de la requête.
- XML : Pour recevoir la réponse en XML, incluez Accept: application/xml dans l'en-tête de la requête.

## Sommaire
- [Récupération de tous les Films](#récupération-de-tous-les-films)
- [Récupération d'un Film Spécifique](#récupération-dun-film-spécifique)
- [Création d'un Nouveau Film](#création-dun-nouveau-film)
- [Modification d'un Film](#modification-dun-film)
- [Suppression d'un Film](#suppression-dun-film)

## Récupération de tous les Films
**GET** `/film/list`

Cette route permet de récupérer une liste de tous les films.

**Réponse :**
```json
{
    "films": [
        {
            "id": 1,
            "nom": "Nom du Film",
            "description": "Description du Film",
            "dateDeParution": "YYYY-MM-DD",
            "note": 5
        },
        ...
    ]
}
```

## Récupération d'un Film Spécifique
**GET** `/film/{id}`
- `{id}` : Identifiant du Film

Cette route permet de récupérer un film spécifique.

**Réponse :**
```json
{
    "film": {
        "id": 1,
        "nom": "Nom du Film",
        "description": "Description du Film",
        "dateDeParution": "YYYY-MM-DD",
        "note": 5
    }
}
```

## Création d'un Nouveau Film
**POST** `/film`

Cette route permet de créer un nouveau film.

**Paramètres :**
```json
{
    "nom": "Nom du Film",
    "description": "Description du Film",
    "dateDeParution": "YYYY-MM-DD",
    "note": 5
}
```

**Réponse :**
```json
{
    "message": "Film created successfully"
}

```

## Modification d'un Film
**PUT** `/film/{id}`
- `{id}` : Identifiant du Film

Cette route permet de modifier un film spécifique.

**Paramètres :**
```json
{
    "nom": "Nom du Film",
    "description": "Description du Film",
    "dateDeParution": "YYYY-MM-DD",
    "note": 5
}
```

**Réponse :**
```json
{
    "message": "Film updated successfully"
}
```

## Suppression d'un Film
**DELETE** `/film/{id}`
- `{id}` : Identifiant du Film

Cette route permet de supprimer un film spécifique.

**Réponse :**
```json
{
    "message": "Film deleted successfully"
}
```