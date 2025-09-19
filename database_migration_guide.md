# 🗄️ Database Migration Guide - phpMyAdmin to Cloud

This guide helps you migrate your Flower Garden Hotels database from local phpMyAdmin to cloud deployment.

## 📋 Prerequisites

- Local XAMPP with phpMyAdmin running
- Database: `ellaflowergarden`
- Cloud deployment platform account (Vercel, Railway, etc.)

## 🎯 Migration Options

### Option 1: Export/Import Method (Recommended)

#### Step 1: Export from phpMyAdmin

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `ellaflowergarden`
3. Click "Export" tab
4. Choose "Custom" export method
5. Select all tables
6. Choose "SQL" format
7. Check "Add DROP TABLE / VIEW / PROCEDURE / FUNCTION / EVENT / TRIGGER statement"
8. Check "Add CREATE TABLE statement"
9. Check "Add INSERT statement"
10. Click "Go" to download

#### Step 2: Set Up Cloud Database

Choose one of these options:

**A. PlanetScale (Recommended)**

1. Go to [planetscale.com](https://planetscale.com)
2. Create free account
3. Create new database: `flower-garden-hotels`
4. Get connection details

**B. Railway MySQL**

1. Go to [railway.app](https://railway.app)
2. Create new project
3. Add MySQL database service
4. Get connection details

**C. Supabase (PostgreSQL)**

1. Go to [supabase.com](https://supabase.com)
2. Create new project
3. Get connection details

#### Step 3: Import to Cloud Database

1. Use cloud database's SQL editor or import tool
2. Paste your exported SQL
3. Run the import

#### Step 4: Update Application Configuration

Update your `config.php` with cloud database details:

```php
// For PlanetScale
$host = 'your-planetscale-host';
$dbname = 'your-database-name';
$username = 'your-username';
$password = 'your-password';

// For Railway
$host = 'your-railway-mysql-host';
$dbname = 'railway';
$username = 'root';
$password = 'your-railway-password';
```

### Option 2: Remote phpMyAdmin Service

#### Using Railway for phpMyAdmin

1. Deploy phpMyAdmin as separate Railway service
2. Connect your app to the same database
3. Access phpMyAdmin interface remotely

#### Using Docker for phpMyAdmin

```yaml
# docker-compose.yml
version: "3.8"
services:
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    environment:
      PMA_HOST: your-database-host
      PMA_USER: your-username
      PMA_PASSWORD: your-password
    ports:
      - "8080:80"
```

### Option 3: Direct Connection (Not Recommended)

⚠️ **Security Risk**: Only for development/testing

```php
// config.php - Direct connection to local database
$host = 'your-public-ip'; // Your computer's public IP
$dbname = 'ellaflowergarden';
$username = 'root';
$password = 'your-password';
```

## 🔧 Database Export Script

I'll create a script to help you export your database properly.

## 📊 Environment Variables for Deployment

### Vercel Environment Variables

```env
DB_HOST=your-cloud-database-host
DB_NAME=your-database-name
DB_USER=your-username
DB_PASS=your-password
WEBSITE_URL=https://your-app.vercel.app
```

### Railway Environment Variables

```env
DATABASE_URL=mysql://username:password@host:port/database
DB_HOST=your-railway-host
DB_NAME=railway
DB_USER=root
DB_PASS=your-railway-password
```

## 🚀 Step-by-Step Migration Process

### 1. Export Your Current Database

- Use phpMyAdmin export feature
- Download SQL file
- Verify all tables are included

### 2. Choose Cloud Database Provider

- **PlanetScale**: MySQL-compatible, free tier
- **Railway**: MySQL, includes hosting
- **Supabase**: PostgreSQL, free tier

### 3. Import to Cloud Database

- Use provider's import tool
- Run your SQL file
- Verify data integrity

### 4. Update Application

- Modify `config.php`
- Set environment variables
- Test connection

### 5. Deploy Application

- Deploy to Vercel/Railway
- Configure environment variables
- Test full functionality

## 🔍 Verification Steps

After migration, verify:

- [ ] All tables created successfully
- [ ] Data imported correctly
- [ ] Application connects to cloud database
- [ ] Admin login works
- [ ] User registration works
- [ ] Booking system functions
- [ ] Images display correctly

## 🆘 Troubleshooting

### Common Issues

- **Connection refused**: Check host/port
- **Access denied**: Verify username/password
- **Table doesn't exist**: Re-run import
- **Data missing**: Check import logs

### Solutions

- Double-check connection details
- Verify database permissions
- Re-export and re-import if needed
- Check cloud provider documentation

## 📞 Support

- Check cloud provider documentation
- Review error logs
- Test locally first
- Use database connection test tools

---

**🌸 Your Flower Garden Hotels database will be successfully migrated to the cloud! 🌸**
