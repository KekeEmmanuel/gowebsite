# Deployment Steps 4-6 Guide

## Step 4: Update/create the `create-env.php` file

1. **Open File Manager in cPanel:**
   - In cPanel, click on "File Manager" (under Files section)

2. **Navigate to public_html:**
   - In the path bar, type: `public_html` and click "Go"
   - Or navigate to: `/home/gotzsafari/public_html`

3. **Find and edit `create-env.php`:**
   - Look for the file `create-env.php` in the file list
   - Click on it to select it
   - Click the "Edit" button (or "Edit Code" button)

4. **Copy the content:**
   - Open the file `/Users/mdegy/goweb/backend/create-env.php` in your local editor
   - Copy ALL the content (lines 1-84)

5. **Paste into cPanel editor:**
   - Delete any existing content in the cPanel editor
   - Paste the copied content
   - Click "Save Changes"

## Step 5: Execute the script to create `.env` file

1. **Open the script in your browser:**
   - Navigate to: `https://www.gotzsafari.com/create-env.php`
   - You should see a success message: "✓ .env file created successfully!"

2. **Verify the file was created:**
   - In File Manager, navigate to: `/home/gotzsafari/laravel`
   - You should see a `.env` file there

## Step 6: Run the remaining deployment commands

1. **Open Terminal in cPanel:**
   - In cPanel, click on "Terminal" (under Advanced section)

2. **Run the following commands one by one:**

```bash
cd /home/gotzsafari/laravel
```

```bash
composer install --no-dev --optimize-autoloader
```
*(This may take a few minutes - it downloads all PHP dependencies)*

```bash
php artisan key:generate
```
*(This generates the application encryption key)*

```bash
php artisan migrate --force
```
*(This creates all database tables)*

```bash
npm install && npm run build
```
*(This installs Node.js dependencies and builds frontend assets - may take several minutes)*

```bash
php artisan storage:link
```
*(This creates a symbolic link for file storage)*

3. **Clean up deployment scripts (IMPORTANT for security):**

```bash
rm /home/gotzsafari/public_html/deploy-cpanel.php
rm /home/gotzsafari/public_html/create-env.php
```

## Verification

After completing all steps, visit:
- `https://www.gotzsafari.com` - Should show your Laravel application

If you encounter any errors, check:
- `/home/gotzsafari/laravel/storage/logs/laravel.log` for application errors
- Terminal output for command errors

