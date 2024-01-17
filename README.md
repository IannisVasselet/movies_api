
# MOVIES_API


## Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- Docker 🐳🐳
- Docker Compose 
- Testeur d'API (Postman, Insomnia, etc.) Insomnia est recommandé.

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

## Configurer API d'authentification

### Générer les clés

1. Générez les clés SSH :

   ```bash
   docker-compose exec web mkdir -p config/jwt
   docker-compose exec web openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
   docker-compose exec web openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
   ```

Comming soon...

## Dociumentation API via Swagger

Pour accéder à la documentation de l'API, ouvrez votre navigateur et allez à `http://localhost:8000/api/doc`.

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
- Recherche de films (GET /film/search/{query}) : Recherche des films par nom ou description. Remplacez {query} par la requête de recherche.
- Liste des films par catégorie (GET /category/list) : Renvoie une liste de tous les films d'une catégorie spécifique. Accepte application/json et application/xml.
- Upload du poster d'un film (POST /film/{id}/poster) : Upload le poster d'un film spécifique. Remplacez {id} par l'ID du film. Le corps de la requête doit être au format JSON.

## Formats de Réponse

- JSON : Pour recevoir la réponse en JSON, incluez Accept: application/json dans l'en-tête de la requête.
- XML : Pour recevoir la réponse en XML, incluez Accept: application/xml dans l'en-tête de la requête.
- Par défaut, l'API répondra en JSON si aucun format n'est spécifié.
- Le JSON-HAL est utilisé pour les réponses JSON.

## Sommaire
- [Récupération de tous les Films](#récupération-de-tous-les-films)
- [Récupération d'un Film Spécifique](#récupération-dun-film-spécifique)
- [Création d'un Nouveau Film](#création-dun-nouveau-film)
- [Modification d'un Film](#modification-dun-film)
- [Suppression d'un Film](#suppression-dun-film)
- [Recherche de Films](#recherche-de-films)
- [Upload du Poster d'un Film](#upload-du-poster-dun-film)
- [Liste des Films par Catégorie](#liste-des-films-par-catégorie)
- [Liste une Catégorie](#liste-une-catégorie)
- [Création d'une Catégorie](#création-dune-catégorie)
- [Modification d'une Catégorie](#modification-dune-catégorie)
- [Suppression d'une Catégorie](#suppression-dune-catégorie)

## Récupération de tous les Films
**GET** `api/film/list`

Cette route permet de récupérer une liste de tous les films.
La pagination est activée.
Pour récupérer une page spécifique, ajoutez le paramètre `page` à la requête. Et pour spécifier le nombre de films par page, ajoutez le paramètre `pageSize` à la requête. Par défaut, `page` est égal à 1 et `pageSize` est égal à 10.
Par exemple, pour récupérer la page 2, utilisez `/film/list?page=2`.

**Réponse :**
```json
{
    "films": [
        {
            "id": 1,
            "nom": "Nom du Film",
            "description": "Description du Film",
            "dateDeParution": "YYYY-MM-DD",
            "note": 5,
              "category": [
                {
                  "name": "Nom de la Catégorie"
                }
              ],
              "_links": {
                "self": {
                  "href": "/api/film/1"
                }
              }
        },
        ...
    ]
}
```

## Récupération d'un Film Spécifique
**GET** `api/film/{id}`
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
        "note": 5,
          "category": [
            {
              "name": "Nom de la Catégorie"
            }
          ],
          "_links": {
            "self": {
              "href": "/api/film/1"
            }
          }
    }
}
```

## Création d'un Nouveau Film
**POST** `api/film`

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
  "message": "Film created successfully",
  "film": {
    "id": 1,
    "nom": "Nom du Film",
    "description": "Description du Film",
    "date_de_parution": "2013-02-14T18:53:44+00:00",
    "note": 5,
    "_links": {
      "self": {
        "href": "/api/film/1"
      }
    }
  }
}

```

## Modification d'un Film
**PUT** `api/film/{id}`
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
**DELETE** `api/film/{id}`
- `{id}` : Identifiant du Film

Cette route permet de supprimer un film spécifique.

**Réponse :**
```json
{
    "message": "Film deleted successfully"
}
```

## Recherche de Films
**GET** `api/film/search/{query}`
- `{query}` : Requête de Recherche

Cette route permet de rechercher des films par nom ou description.

**Réponse :**
```json
{
      "films": [
        {
          "id": 2,
          "nom": "Nom du Film",
          "description": "Description du Film",
          "date_de_parution": "1991-02-07T15:15:06+00:00",
          "note": 5,
          "category": [
            {
              "name": "Nom de la Catégorie"
            }
          ],
          "_links": {
            "self": {
              "href": "/api/film/2"
            }
          }
        },
        ...
    ]
}
```

## Upload du Poster d'un Film
**POST** `api/film/{id}/poster`
- `{id}` : Identifiant du Film
- `poster` : Poster du Film

Cette route permet d'uploader le poster d'un film spécifique.

**Réponse :**
```json
{
	"film": {
		"id": 1,
		"nom": "Nom du Film",
		"description": "Description du Film",
		"date_de_parution": "2013-02-14T08:14:03+00:00",
		"note": 2,
		"image": "poster.jpg",
		"category": [
			{
				"name": "Nom de la Catégorie"
			}
		],
		"_links": {
			"self": {
				"href": "/api/film/1"
			}
		}
	}
}
```

## Liste des Films par Catégorie
**GET** `api/category/list`

Cette route permet de récupérer une liste de tous les films d'une catégorie spécifique.

**Réponse :**
```json
{
  "categories": [
    {
      "name": "Nom de la Catégorie",
      "films": [
        {
          "nom": "Nom du Film"
        }
        ]
    }
]
}
```

## Liste une Catégorie
**GET** `api/category/{name}`
- `{name}` : Nom de la Catégorie

Cette route permet de récupérer une catégorie spécifique.

**Réponse :**
```json
{
  "category": {
    "name": "Nom de la Catégorie",
    "films": [
      {
        "nom": "Nom du Film"
      }, 
        ...
    ]
  }
}
```

## Création d'une Catégorie
**POST** `api/category`

Cette route permet de créer une nouvelle catégorie.

**Paramètres :**
```json
{
    "name": "Nom de la Catégorie"
}
```

**Réponse :**
```json
{
  "message": "Category created successfully",
  "category": {
    "name": "Nom de la Catégorie"
  }
}
```

## Modification d'une Catégorie
**PUT** `api/category/{name}`
- `{name}` : Nom de la Catégorie

Cette route permet de modifier une catégorie spécifique.

**Paramètres :**
```json
{
    "name": "Nouveaux Nom de la Catégorie"
}
```

**Réponse :**
```json
{
  "message": "Category updated successfully",
  "category": {
    "name": "Nouveaux Nom de la Catégorie",
    "films": []
  }
}
```

## Suppression d'une Catégorie
**DELETE** `api/category/{name}`
- `{name}` : Nom de la Catégorie

Cette route permet de supprimer une catégorie spécifique.

**Réponse :**
```json
{
  "message": "Category deleted successfully"
}
```


