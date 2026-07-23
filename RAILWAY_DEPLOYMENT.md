# Railway Deployment Guide

## Overview

This guide will help you deploy the Haland PetCare application to Railway.

## Prerequisites

1. **Railway Account**: Sign up at [railway.app](https://railway.app)
2. **GitHub Account**: Your code should be in a GitHub repository
3. **Railway CLI** (optional): Install via `npm install -g @railway/cli`

## Quick Deploy

### Step 1: Push to GitHub

```bash
git add .
git commit -m "Ready for Railway deployment"
git push origin main
```

### Step 2: Create Railway Project

1. Go to [railway.app](https://railway.app)
2. Click "New Project"
3. Select "Deploy from GitHub repo"
4. Select your repository
5. Railway will auto-detect Laravel

### Step 3: Add PostgreSQL Database

1. In your Railway project, click "New"
2. Select "Database" → "PostgreSQL"
3. Railway will auto-create a PostgreSQL instance
4. Copy the database credentials

### Step 4: Configure Environment Variables

Go to your app service → Settings → Variables and add:

```env
# Application
APP_NAME="Haland PetCare"
APP_ENV=production
APP_DEBUG=false
APP_URL=${RAILWAY_STATIC_URL}

# Database (Railway auto-provides these)
DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (optional - configure your SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@halandpetcare.com"
MAIL_FROM_NAME="Haland PetCare"
```

### Step 5: Deploy

Railway will automatically:
1. Install PHP dependencies
2. Build frontend assets
3. Run migrations
4. Start the application

## Manual Deploy with CLI

### Install Railway CLI

```bash
npm install -g @railway/cli
```

### Login

```bash
railway login
```

### Initialize Project

```bash
railway init
```

### Add PostgreSQL

```bash
railway add --plugin postgresql
```

### Set Environment Variables

```bash
railway variables set APP_NAME="Haland PetCare"
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set DB_CONNECTION=pgsql
railway variables set SESSION_DRIVER=database
railway variables set CACHE_STORE=database
railway variables set QUEUE_CONNECTION=database
```

### Deploy

```bash
railway up
```

### Run Migrations

```bash
railway run php artisan migrate --force
```

### Seed Database (First Deploy Only)

```bash
railway run php artisan db:seed
```

## Post-Deployment Checklist

- [ ] Verify application is running
- [ ] Check database connection
- [ ] Run migrations
- [ ] Seed database with default data
- [ ] Test login with default accounts
- [ ] Configure custom domain (optional)
- [ ] Set up environment variables for mail
- [ ] Test email notifications

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Owner | owner@halandpetcare.com | password |
| Dokter | dokter@halandpetcare.com | password |
| Kasir | kasir@halandpetcare.com | password |
| Admin | admin@halandpetcare.com | password |

## Troubleshooting

### Application Won't Start

1. Check logs: Railway Dashboard → Your Service → Deployments → View Logs
2. Verify environment variables are set correctly
3. Ensure database is connected

### Database Connection Error

1. Verify PostgreSQL plugin is added
2. Check DB_* environment variables
3. Ensure migrations have run

### Build Fails

1. Check build logs in Railway Dashboard
2. Verify Node.js and PHP versions
3. Ensure all dependencies are installed

### Assets Not Loading

1. Run `npm run build` locally
2. Commit the `public/build` directory
3. Or configure Railway to build assets

## Custom Domain

1. Go to Railway Dashboard → Your Service → Settings
2. Click "Networking" → "Custom Domain"
3. Add your domain (e.g., `halandpetcare.com`)
4. Configure DNS records as shown
5. Railway will auto-provision SSL

## Scaling

Railway auto-scales based on traffic. For manual scaling:

1. Go to Railway Dashboard → Your Service → Settings
2. Adjust "Instances" count
3. Set resource limits (CPU, RAM)

## Monitoring

- **Logs**: Railway Dashboard → Your Service → Deployments
- **Metrics**: Railway Dashboard → Your Service → Metrics
- **Health Check**: `https://your-app.up.railway.app/health`

## Backup

Railway provides automatic backups for PostgreSQL. To create manual backup:

```bash
railway run pg_dump $DATABASE_URL > backup.sql
```

## Rollback

To rollback to a previous deployment:

1. Go to Railway Dashboard → Your Service → Deployments
2. Find the working deployment
3. Click "Rollback to this deployment"

## Cost Optimization

- Railway offers free tier with limited hours
- Use the $5 hobby plan for production
- Monitor usage in Railway Dashboard

## Support

- Railway Documentation: [docs.railway.app](https://docs.railway.app)
- Railway Discord: [discord.gg/railway](https://discord.gg/railway)
- Laravel Documentation: [laravel.com/docs](https://laravel.com/docs)
