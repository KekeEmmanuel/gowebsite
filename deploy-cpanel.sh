#!/bin/bash

# cPanel Deployment Script for Laravel Application
# Run this script via SSH after uploading files to cPanel

echo "🚀 Starting Laravel cPanel Deployment..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Get the current directory
CURRENT_DIR=$(pwd)
echo -e "${YELLOW}Current directory: $CURRENT_DIR${NC}"

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: artisan file not found. Are you in the Laravel root directory?${NC}"
    exit 1
fi

# Step 1: Install Composer dependencies
echo -e "\n${GREEN}Step 1: Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -ne 0 ]; then
    echo -e "${RED}Error: Composer install failed${NC}"
    exit 1
fi

# Step 2: Install NPM dependencies
echo -e "\n${GREEN}Step 2: Installing NPM dependencies...${NC}"
if [ -f "package.json" ]; then
    npm install --production
    if [ $? -ne 0 ]; then
        echo -e "${YELLOW}Warning: NPM install failed, but continuing...${NC}"
    fi
else
    echo -e "${YELLOW}No package.json found, skipping NPM install${NC}"
fi

# Step 3: Build frontend assets
echo -e "\n${GREEN}Step 3: Building frontend assets...${NC}"
if [ -f "package.json" ]; then
    npm run build
    if [ $? -ne 0 ]; then
        echo -e "${YELLOW}Warning: NPM build failed, but continuing...${NC}"
    fi
else
    echo -e "${YELLOW}No package.json found, skipping build${NC}"
fi

# Step 4: Check if .env exists
echo -e "\n${GREEN}Step 4: Checking environment file...${NC}"
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        echo -e "${YELLOW}.env not found, copying from .env.example${NC}"
        cp .env.example .env
        echo -e "${YELLOW}⚠️  Please edit .env file with your production settings!${NC}"
    else
        echo -e "${RED}Error: .env file not found and .env.example doesn't exist${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}.env file exists${NC}"
fi

# Step 5: Generate app key if not set
echo -e "\n${GREEN}Step 5: Checking APP_KEY...${NC}"
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo -e "${YELLOW}APP_KEY not set, generating...${NC}"
    php artisan key:generate
else
    echo -e "${GREEN}APP_KEY already set${NC}"
fi

# Step 6: Set permissions
echo -e "\n${GREEN}Step 6: Setting file permissions...${NC}"
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}Permissions set${NC}"

# Step 7: Clear and cache config
echo -e "\n${GREEN}Step 7: Optimizing Laravel...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 8: Run migrations (optional - uncomment if needed)
# echo -e "\n${GREEN}Step 8: Running database migrations...${NC}"
# read -p "Do you want to run migrations? (y/n) " -n 1 -r
# echo
# if [[ $REPLY =~ ^[Yy]$ ]]; then
#     php artisan migrate --force
# fi

# Step 9: Run seeders (optional - uncomment if needed)
# echo -e "\n${GREEN}Step 9: Running database seeders...${NC}"
# read -p "Do you want to run seeders? (y/n) " -n 1 -r
# echo
# if [[ $REPLY =~ ^[Yy]$ ]]; then
#     php artisan db:seed --force
# fi

echo -e "\n${GREEN}✅ Deployment script completed!${NC}"
echo -e "\n${YELLOW}Next steps:${NC}"
echo "1. Edit .env file with your production settings"
echo "2. Run migrations: php artisan migrate --force"
echo "3. Run seeders: php artisan db:seed --force"
echo "4. Verify file permissions on storage/ and bootstrap/cache/"
echo "5. Test your website!"

