#!/bin/bash

# This script sets up a PHP development environment using Docker Compose and runs a PHP server.
docker compose up -d

# Wait for the Docker containers to be ready
# sleep 10

# Run migrations to set up the database schema
# php spark migrate --all 

# Create a super admin user
# Ensure the user is active by setting the 'active' field to 1
# This is done in the CreateSuperAdmin command.
# If the command is not available, you can create a super admin user manually.
# Uncomment the line below to create a super admin user.
# Note: Ensure that the CreateSuperAdmin command is properly defined in your application.
# If you have a custom command to create a super admin, use that instead.
# php spark admin:create-superadmin

# Start the PHP server
php spark serve
