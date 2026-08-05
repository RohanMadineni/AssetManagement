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

Useful commands: 

Laravel: 
  - Clear cache: php artisan optimize:clear
  - Index assets: php artisan app:reindex-assets
  - Notification queue consumer: php artisan rabbitmq:consume-notifications
  - Elastic queue consumer: php artisan app:consume-elastic-queue
     


