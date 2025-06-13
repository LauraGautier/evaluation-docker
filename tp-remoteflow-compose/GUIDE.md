# 🚀 Déploiement RemoteFlow

## Fichiers nécessaires
```
remoteflow-deploy/
├── docker-compose.yml
├── .env
├── proxy/nginx.conf
└── README.md
```

## Déploiement

### 1. Prérequis
```bash
# DNS local
echo "127.0.0.1 remoteflow.test" | sudo tee -a /etc/hosts

# Vérifier ports libres
netstat -tlnp | grep -E ':(8002|8081|9000)'
```

### 2. Déploiement
```bash
# Démarrer sans les profiles
docker compose up -d

# Avec les profiles
docker compose --profile dev --profile monitoring up -d

# Migrations Laravel
docker compose exec app php artisan migrate
```

### 3. Accès
- **App** : http://remoteflow.test:8002
- **Adminer** : http://localhost:8081  
- **Portainer** : http://localhost:9000

## Reset complet
```bash
docker compose down -v
docker compose up -d
docker compose exec app php artisan migrate
```
