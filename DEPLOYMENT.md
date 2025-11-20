# Railway Deployment Guide

## Quick Start

Your app is ready to deploy! Follow these steps:

## Step 1: Create Railway Account & Project

1. Go to https://railway.app
2. Click "Start a New Project"
3. Sign up with GitHub (use your KekeEmmanuel account)
4. Click "Deploy from GitHub repo"
5. Select repository: `KekeEmmanuel/gowebsite`
6. Railway will auto-detect Laravel and start building

## Step 2: Add PostgreSQL Database

1. In your Railway project dashboard, click **"+ New"**
2. Select **"Database"** → **"Add PostgreSQL"**
3. Railway creates the database automatically
4. The connection variables will be auto-injected

## Step 3: Configure Environment Variables

Go to your **service** → **Variables** tab and add:

```env
APP_NAME="Go Tanzania Safari"
APP_ENV=production
APP_KEY=base64:vz7dtdQnkwYhcxC/RZ55cR6r2pWghC5tU5SGEbc5ouk=
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
```

**Important:** 
- Replace `your-app-name` with your actual Railway domain (you'll get this after first deploy)
- The `${{Postgres.*}}` variables are automatically provided by Railway when you link the database

## Step 4: Link Database to Service

1. In your **service** settings
2. Go to **"Settings"** → **"Variables"**
3. Railway should auto-detect the PostgreSQL service
4. If not, click **"Add Reference"** and select your PostgreSQL service

## Step 5: Deploy

1. Railway will automatically deploy when you connect the repo
2. Watch the build in the **"Deployments"** tab
3. First build takes 5-10 minutes
4. Subsequent deploys are faster

## Step 6: Get Your Live URL

1. After deployment, go to **"Settings"** → **"Networking"**
2. Click **"Generate Domain"** to get a public URL
3. Copy the URL (e.g., `https://your-app-name.up.railway.app`)
4. Update `APP_URL` in variables with this exact URL
5. Redeploy (Railway will auto-redeploy when you save variables)

## Step 7: Verify Deployment

1. Visit your Railway URL
2. You should see the marketing site homepage
3. Visit `/admin/login` to access admin panel
4. Default admin credentials (from seeder):
   - Email: `admin@gotanzaniasafari.com`
   - Password: `password` (change this immediately!)

## Troubleshooting

### Build Fails
- Check build logs in Railway dashboard
- Ensure all environment variables are set
- Verify PostgreSQL is linked

### Database Connection Error
- Verify PostgreSQL service is running
- Check DB_* variables are correctly set
- Ensure database is linked to your service

### 500 Error After Deploy
- Check Railway logs
- Verify APP_KEY is set
- Ensure migrations ran successfully

### Assets Not Loading
- Check that `npm run build` completed
- Verify Vite build succeeded
- Check public directory permissions

## Admin Access

After deployment, access admin at: `https://your-app-name.up.railway.app/admin/login`

**Default Credentials:**
- Email: `admin@gotanzaniasafari.com`
- Password: `password`

**⚠️ IMPORTANT: Change the password immediately after first login!**

## What Happens Automatically

✅ Dependencies installed (Composer + NPM)
✅ Assets built (Vite)
✅ Migrations run
✅ Database seeded (admin user, sample data)
✅ App served on Railway's infrastructure

## Need Help?

Check Railway logs in the dashboard for detailed error messages.

