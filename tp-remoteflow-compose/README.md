# Déploiement Docker de mon projet fil rouge avec Reverse Proxy

## Intro

Déploiement de mon application RemoteFlow (Laravel) avec une architecture microservices utilisant Docker Compose, un reverse proxy Nginx et une base de données MySQL.

## Architecture

```
Internet → [Nginx Proxy:8002] → [Laravel App] → [MySQL DB]
               ↓                    ↓              ↓
          frontend_net        backend_net    backend_net
```

### Services

- **`proxy`** : Nginx (reverse proxy) - Point d'entrée unique
- **`app`** : Application Laravel RemoteFlow 
- **`database`** : MySQL 8.0 avec persistance

### Profiles

```yaml
# Dev
adminer:
  image: adminer
  profiles: ["dev"]
  ports: ["8081:8080"]

# Monitoring
portainer:
  image: portainer/portainer-ce:latest
  profiles: ["monitoring"]
  ports: ["9000:9000"]
```

### Réseaux

- **`frontend_net`** : Communication externe ↔ proxy
- **`backend_net`** : Communication interne (proxy ↔ app ↔ db)

### Volumes

- **`db_data`** : Persistance des données MySQL

## Images Docker

- **Application** : `elelya/remoteflow-app:latest`
- **BDD** : `mysql:8.0` (officielle)
- **Reverse Proxy** : `nginx:alpine` (officielle)

## Déploiement

### Lancement classique
```bash
# Démarrer
docker compose up -d

# Ne pas oublier les migrations Laravel !!
docker compose exec app php artisan migrate
```

### Accès à l'appli
- **Application principale** : http://remoteflow.test:8002
- **Status du proxy** : http://remoteflow.test:8002/proxy-status

## 🛠️ Configuration

### Mes variables d'environnement (.env)
```bash
DOMAIN_NAME=remoteflow.test
PROXY_PORT=8002

MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=remoteflow_db
MYSQL_USER=remoteflow
MYSQL_PASSWORD=remoteflow

APP_NAME=RemoteFlow
APP_ENV=local
APP_DEBUG=true
APP_URL=http://remoteflow.test:8002
```

### Structure de mes fichiers

```
tp-remoteflow-compose/
├── docker-compose.yml    # Configuration de mes services
├── .env                  # Variables d'environnement
├── proxy/
│   └── nginx.conf       # Configuration de mon reverse proxy
├── app/
│   └── Dockerfile       # Build de mon image Laravel
└── README.md            # Ce fichier
```
