# 🚀 Flower Garden Hotels - Deployment Guide

This guide covers multiple deployment options for your PHP/MySQL hotel reservation system.

## 📋 Prerequisites

- GitHub repository: [https://github.com/dilminekanayaka/y2s2-software-testing-assignment-hotel-reservation-system.git](https://github.com/dilminekanayaka/y2s2-software-testing-assignment-hotel-reservation-system.git)
- Database setup (MySQL/MariaDB)
- Image files in `uploads/Hotels/` directory

## 🌟 Option 1: Railway (Recommended - Easiest)

Railway is perfect for PHP/MySQL applications and offers free hosting.

### Step 1: Create Railway Account

1. Go to [railway.app](https://railway.app)
2. Sign up with GitHub
3. Connect your repository

### Step 2: Deploy Project

1. Click "New Project"
2. Select "Deploy from GitHub repo"
3. Choose your repository
4. Railway will automatically detect PHP and deploy

### Step 3: Add Database

1. In your project dashboard, click "New"
2. Select "Database" → "MySQL"
3. Railway will provide connection details

### Step 4: Configure Environment Variables

Add these in Railway dashboard:

```
DB_HOST=your-mysql-host
DB_NAME=your-database-name
DB_USER=your-username
DB_PASS=your-password
```

### Step 5: Import Database

1. Use Railway's MySQL console or external tool
2. Import `complete_database_setup.sql`
3. Run `update_database_images.php` to set local image paths

### Step 6: Access Your App

Railway will provide a URL like: `https://your-app.railway.app`

---

## 🌐 Option 2: Vercel (Advanced - Requires Conversion)

Vercel doesn't natively support PHP, but we can use serverless functions.

### Step 1: Install Vercel CLI

```bash
npm install -g vercel
```

### Step 2: Login to Vercel

```bash
vercel login
```

### Step 3: Deploy

```bash
vercel
```

### Step 4: Configure Environment Variables

In Vercel dashboard, add:

```
DB_HOST=your-external-db-host
DB_NAME=your-database-name
DB_USER=your-username
DB_PASS=your-password
```

### Step 5: Set up External Database

Use services like:

- **PlanetScale** (MySQL-compatible)
- **Supabase** (PostgreSQL)
- **Railway MySQL** (separate service)

---

## 🐳 Option 3: Heroku

### Step 1: Create Heroku App

```bash
heroku create flower-garden-hotels
```

### Step 2: Add PHP Buildpack

```bash
heroku buildpacks:set heroku/php
```

### Step 3: Add MySQL Addon

```bash
heroku addons:create cleardb:ignite
```

### Step 4: Deploy

```bash
git push heroku main
```

---

## ☁️ Option 4: DigitalOcean App Platform

### Step 1: Create App

1. Go to DigitalOcean App Platform
2. Create new app from GitHub
3. Select your repository

### Step 2: Configure

- Runtime: PHP
- Build command: `composer install`
- Run command: `php -S 0.0.0.0:$PORT`

### Step 3: Add Database

- Add MySQL database service
- Configure connection string

---

## 🗄️ Database Setup for All Platforms

### 1. Import Schema

```sql
-- Run complete_database_setup.sql
mysql -u username -p database_name < complete_database_setup.sql
```

### 2. Update Image Paths

Visit: `https://your-domain.com/update_database_images.php`

### 3. Verify Setup

- Check admin login: `admin` / `admin123`
- Test booking flow
- Verify image display

---

## 🔧 Environment Configuration

### Required Environment Variables

```env
DB_HOST=your-database-host
DB_NAME=your-database-name
DB_USER=your-username
DB_PASS=your-password
WEBSITE_URL=https://your-domain.com
```

### Optional Variables

```env
EMAIL_FROM_NAME=Flower Garden Hotels
EMAIL_FROM_EMAIL=noreply@flowergarden.com
DEBUG_MODE=false
```

---

## 📁 File Structure for Deployment

```
flower-garden-hotels/
├── api/                    # API endpoints (for Vercel)
├── public/                 # Static files (for Vercel)
├── admin/                  # Admin portal
├── uploads/Hotels/         # Hotel images
├── complete_database_setup.sql
├── config.php              # Database config
├── home.php               # Main pages
├── hotels.php
├── booking.php
├── package.json           # For Vercel
├── composer.json          # For PHP platforms
├── railway.json           # For Railway
├── vercel.json            # For Vercel
└── nixpacks.toml          # For Railway
```

---

## 🚨 Important Notes

### Image Handling

- Images are stored in `uploads/Hotels/`
- Database references local paths
- Ensure proper file permissions

### Security

- Change default admin password
- Use HTTPS in production
- Configure proper CORS settings

### Performance

- Enable PHP OPcache
- Use CDN for images
- Optimize database queries

---

## 🎯 Recommended Deployment Flow

1. **Start with Railway** (easiest for PHP/MySQL)
2. **Set up MySQL database**
3. **Import database schema**
4. **Configure environment variables**
5. **Test the application**
6. **Update image paths**
7. **Go live!**

---

## 🆘 Troubleshooting

### Common Issues

- **Database connection**: Check environment variables
- **Image not loading**: Verify file paths and permissions
- **Email not sending**: Configure SMTP settings
- **Admin login issues**: Check database user table

### Support

- Check platform-specific documentation
- Review error logs
- Test locally first

---

## 🌟 Success Checklist

- [ ] Application deployed and accessible
- [ ] Database imported successfully
- [ ] Admin login working
- [ ] User registration working
- [ ] Hotel search functional
- [ ] Booking process complete
- [ ] Images displaying correctly
- [ ] Email notifications working
- [ ] Mobile responsive design
- [ ] HTTPS enabled

---

**🌸 Your Flower Garden Hotels system is now live! 🌸**

For questions or issues, check the platform documentation or create an issue in the GitHub repository.
