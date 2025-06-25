#!/bin/bash

# This script sets up a PHP development environment using Docker Compose and runs a PHP server.
docker compose up -d

# Start the PHP server
php spark serve
