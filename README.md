# Asset Management System

A full-stack asset management platform built with Angular, Laravel, MySQL, Docker, RabbitMQ, Redis, Elasticsearch, Socket.IO, and Flutter

## Architecture Overview

The system consists of:
- Angular 21 frontend
- Laravel 11 backend API
- MySQL database
- Redis caching layer
- Elasticsearch search and indexing
- RabbitMQ event broker
- Node.js Socket.IO realtime server
- Nginx reverse proxy
- electronjs desktop app
- Docker Compose deployment
  
## Docker Services

- frontend
- laravel
- nginx
- redis
- rabbitmq
- realtime-server

**Build: docker compose build**

**Start: docker compose up -d**

### Useful commands: 

Laravel: 
  - Clear cache: php artisan optimize:clear
  - Index assets: php artisan app:reindex-assets
  - Notification queue consumer: php artisan rabbitmq:consume-notifications
  - Elastic queue consumer: php artisan app:consume-elastic-queue


## Limitations
 - Elasticsearch dependency : asset search/indexing depends on Elasticsearch being available
 - Cache consistency : Redis caching means changes must invalidate the appropriate cache entries to prevent stale data
 - RabbitMQ dependency : asynchronous notifications and indexing depend on RabbitMQ consumers being available. 

## Unlimited features 
 - Advanced role/permission management
 - Production-grade monitoring and alerting
 - Full audit history for every asset modification
 - Automated database backup/restore procedures

## Technical debt
 - Automated test coverage could be expanded around the asynchronous event-driven architecture
 - Some responsibilities could be separated further as the application grows
 

## License

Internal Asset Management System 
     


