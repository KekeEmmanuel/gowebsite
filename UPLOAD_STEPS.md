# Step-by-Step Upload Guide

## ✅ Build Complete!
Your frontend assets are ready to upload.

## 📦 Files Ready
- **Build folder**: `public/build/` (1.0 MB, 59 files)
- **ZIP archive**: `public/build.zip` (easier to upload)

## 🚀 Upload Steps

### Step 1: Log into cPanel
1. Go to: `https://www.gotzsafari.com/cpanel`
2. Log in with your credentials

### Step 2: Open File Manager
1. In cPanel, find and click **"File Manager"**
2. Navigate to: `/home/gotzsafari/laravel/public/`
   - You should see files like `index.php`, `robots.txt`, etc.

### Step 3: Delete Old Build (if exists)
1. Look for a folder named `build`
2. If it exists, right-click it → **Delete**
3. Confirm deletion

### Step 4: Upload Build Folder

**Option A: Upload ZIP (Easier)**
1. Click the **"Upload"** button at the top
2. Select the file: `public/build.zip` from your local machine
3. Wait for upload to complete
4. Once uploaded, right-click `build.zip` → **Extract**
5. Delete `build.zip` after extraction

**Option B: Upload Folder Directly**
1. Click the **"Upload"** button
2. Select the entire `build` folder from: `/Users/mdegy/goweb/backend/public/build/`
3. Wait for all files to upload (may take a few minutes)

### Step 5: Set Permissions
1. Right-click on the `build` folder
2. Select **"Change Permissions"**
3. Set to: `755` (or check: Read, Write, Execute for Owner; Read, Execute for Group and Public)
4. Click **"Change Permissions"**

### Step 6: Verify Upload
1. Visit: `https://www.gotzsafari.com/build/manifest.json`
2. If you see JSON content, the upload was successful! ✅

### Step 7: Test Your Site
1. Visit: `https://www.gotzsafari.com`
2. The site should load with all styles and JavaScript working

### Step 8: Clean Up (IMPORTANT - Security!)
Delete these files from `public_html`:
- `create-env.php`
- `run-deployment.php`
- `deploy-cpanel.php`
- `check-laravel.php`
- `test-composer.php`

## 🎉 Done!
Your Laravel application should now be fully deployed and working!

