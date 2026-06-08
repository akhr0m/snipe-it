# Snipe-IT Custom Module - Deployment Guide

## Overview

This is a fork of Snipe-IT with a custom module. This guide explains how to deploy to production or staging environments.

**Repository**: https://github.com/akhr0m/snipe-it  
**Branch**: feature/custom-bast-module  
**Base**: Snipe-IT (Laravel 11)

---

## Prerequisites

Before deploying, ensure you have:

- SSH access to production/staging server
- Git installed on server (`git --version`)
- PHP 8.2+ installed (`php -v`)
- Composer installed (`composer --version`)
- MySQL/PostgreSQL database available
- Node.js 18+ installed (`node -v`)
- npm installed (`npm -v`)
- Sufficient disk space (minimum 2GB)
- Backup procedure in place

---

## Pre-Deployment Checklist

Before starting deployment:

- [ ] Code reviewed and merged to feature branch
- [ ] All tests passing locally
- [ ] Staging deployment successful (if applicable)
- [ ] Database backup created
- [ ] Current production version documented
- [ ] Rollback plan prepared
- [ ] Deployment window scheduled
- [ ] Team members notified
- [ ] Maintenance window scheduled (if needed)

---

## Step-by-Step Deployment

### Step 1: SSH to Server

Connect to your production or staging server:

```bash
ssh -i ~/.ssh/your-key.pem username@your-server-ip
```

Expected: You should be logged into the server

### Step 2: Navigate to Application Directory

```bash
cd /var/www/snipe-it
```

Or wherever your Snipe-IT installation is located.

Verify you're in the correct directory:

```bash
pwd
ls -la
```

Expected: You should see folders like `app/`, `config/`, `database/`, `routes/`, etc.

### Step 3: Pull Latest Code

Fetch and merge the latest code from GitHub:

```bash
git fetch origin
git pull origin feature/custom-bast-module
```

Expected output:
```
From github.com:akhr0m/snipe-it
 * branch            feature/custom-bast-module -> FETCH_HEAD
Updating abc1234..def5678
Fast-forward
 ...files changed
```

If you get a merge conflict message, see **Troubleshooting** section below.

### Step 4: ⭐ CRITICAL: Generate Version Configuration

**This step is MANDATORY!** Do not skip this step.

```bash
./build-version.sh
```

Expected output:
```
📦 Generating version config...
✓ Version file generated

   App Version: v...
   Build: ...
   Hash: ...
   Branch: feature/custom-bast-module
   Time: ...
```

**Why this step?**
- Automatically generates `config/version.php` with deployment metadata
- Captures current git hash, timestamp, and branch information
- Prevents merge conflicts in version configuration
- Ensures version info is always accurate

> [!NOTE]
> **Dynamic Fallback Active**: If this script is skipped or fails during deployment, a dynamic runtime fallback in `AppServiceProvider` will automatically detect the Git version/build details (and cache it in `storage/framework/version_cache.json`), ensuring the footer displays the correct version without causing a blank screen or a `Version - build ()` display.

### Step 5: Install PHP Dependencies

Install or update PHP packages using Composer:

```bash
composer install --no-dev --optimize-autoloader
```

Expected: Composer will show progress and eventually finish with `Generating autoload files`

Note: `--no-dev` excludes development dependencies (testing packages, etc.)

### Step 6: Install and Build Frontend Assets

Install Node.js dependencies and compile assets:

```bash
npm install
npm run production
```

Expected: npm will install packages and then compile JavaScript and CSS for production

This may take 1-3 minutes depending on server speed.

### Step 7: Run Database Migrations

Execute any pending database migrations:

```bash
php artisan migrate --force
```

Expected output:
```
Migration table created successfully.
Migrating: ...
Migrated: ...
```

**Note**: The `--force` flag is required for production (bypasses confirmation prompt)

If you want to see what migrations will run without executing:

```bash
php artisan migrate --pretend
```

### Step 8: Clear and Cache Configuration

Clear all caches and rebuild cached configuration:

```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Expected: Each command should show a success message

This ensures:
- Old cached data is cleared
- Latest configuration is loaded
- Routes are optimized
- Views are pre-compiled

### Step 9: Optimize Application

```bash
php artisan optimize
```

Expected: Should show optimization completed

### Step 10: (Optional) Verify Installation

Test that everything is working correctly:

```bash
php artisan tinker
```

Inside tinker, run:

```php
> config('version')
=> array:8 [...]  // Should show version information

> DB::table('migrations')->count()
=> 42  // Should show number of migrations

> exit()
```

### Step 11: Restart Web Server

Restart your web server to reload PHP code:

#### If using PHP-FPM with Nginx:
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

#### If using Apache:
```bash
sudo systemctl restart apache2
```

#### If using Docker:
```bash
docker-compose restart
# or
docker restart container-name
```

### Step 12: Verify Deployment

Open your browser and navigate to your Snipe-IT domain:

```
https://your-snipe-it-domain.com
```

Check that:
- Page loads without errors
- You can log in
- Dashboard displays correctly
- Custom module features work

---

## Troubleshooting

### Issue: "Permission denied" on build-version.sh

**Error**: 
```
-bash: ./build-version.sh: Permission denied
```

**Solution**:
```bash
chmod +x build-version.sh
./build-version.sh
```

---

### Issue: Git merge conflict

**Error**:
```
CONFLICT (content): Merge conflict in ...
Automatic merge failed; fix conflicts and then commit the result.
```

**Solution**:
```bash
# View conflicting files
git status

# Edit the conflicted file
nano path/to/conflicted/file

# After resolving conflicts, add and commit
git add .
git commit -m "resolve merge conflict"

# Continue with deployment from Step 5
```

---

### Issue: "Class not found" or "Method not found" error

**Error**:
```
Fatal error: Class ... not found
```

**Solution**:
```bash
# Rebuild Composer autoloader
composer dump-autoload -o

# Or clear all caches
php artisan optimize:clear
php artisan cache:clear
```

---

### Issue: Database migration fails

**Error**:
```
PDOException: SQLSTATE[HY000]: General error: ...
```

**Solution**:
```bash
# Check migration status
php artisan migrate:status

# If needed, rollback last migration
php artisan migrate:rollback

# Then try migration again
php artisan migrate --force
```

For production, always restore from backup if migrations fail.

---

### Issue: Assets not loading (404 errors on CSS/JS)

**Error**: 
```
GET https://domain.com/css/app.css 404 (Not Found)
```

**Solution**:
```bash
# Rebuild assets
npm run production

# Clear cache
php artisan cache:clear

# Create storage link if needed
php artisan storage:link
```

---

### Issue: "Procedure entry point not found" or similar

**Error**:
```
Windows PHP errors or library mismatches
```

**Solution**:
```bash
# Update dependencies
composer update

# Clear all caches
php artisan optimize:clear

# Restart PHP and web server
```

---

### Issue: Memory limit exceeded during composer install

**Error**:
```
Fatal error: Allowed memory size of ... exhausted
```

**Solution**:
```bash
# Increase memory limit temporarily
COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev
```

---

## Rollback Procedure

If deployment fails and you need to rollback to the previous version:

### Step 1: Identify Previous Version

```bash
git log --oneline -10
```

Find the commit hash you want to rollback to (usually the second item in the list).

### Step 2: Checkout Previous Version

```bash
git checkout abc1234def5  # Replace with actual commit hash
```

Or go back one commit:

```bash
git checkout HEAD~1
```

### Step 3: Regenerate Version Config

```bash
./build-version.sh
```

### Step 4: Rollback Database (if needed)

```bash
# Check migration status
php artisan migrate:status

# Rollback last batch of migrations
php artisan migrate:rollback
```

### Step 5: Clear Cache

```bash
php artisan optimize:clear
php artisan cache:clear
```

### Step 6: Restart Web Server

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Step 7: Verify Rollback

Test the application to ensure it's working with the previous version.

### Step 8: Document Incident

Document what went wrong and how you fixed it for future reference.

---

## Post-Deployment Verification

After deployment, verify everything is working:

### Application Health

```bash
# Check application status
curl -I https://your-domain.com
# Expected: HTTP/1.1 200 OK

# Check logs for errors
tail -f storage/logs/laravel.log
```

### Database

```bash
# Connect to database
php artisan tinker
> DB::connection()->getPdo()
=> PDOConnection...

> exit()
```

### Custom Module

Verify custom module features:
- [ ] Module dashboard accessible
- [ ] Core features working
- [ ] Data displays correctly
- [ ] No PHP errors in logs

### Performance

```bash
# Check application response time
time curl https://your-domain.com

# Monitor server resources
top
df -h
```

---

## Deployment Checklist (Summary)

Use this checklist during deployment:

- [ ] **Pre**: All pre-deployment checks complete
- [ ] **Step 1**: SSH to server successful
- [ ] **Step 2**: In correct application directory
- [ ] **Step 3**: Code pulled successfully
- [ ] **Step 4**: `./build-version.sh` executed
- [ ] **Step 5**: `composer install` completed
- [ ] **Step 6**: `npm install && npm run production` completed
- [ ] **Step 7**: `php artisan migrate --force` completed
- [ ] **Step 8**: Cache cleared and rebuilt
- [ ] **Step 9**: `php artisan optimize` completed
- [ ] **Step 10**: (Optional) Verification with tinker successful
- [ ] **Step 11**: Web server restarted
- [ ] **Step 12**: Application verified in browser
- [ ] **Post**: Post-deployment verification complete

---

## Quick Reference: One-Liner Deployment

For experienced developers, here's the complete deployment sequence in one command:

```bash
cd /var/www/snipe-it && \
git pull origin feature/custom-bast-module && \
./build-version.sh && \
composer install --no-dev --optimize-autoloader && \
npm install && npm run production && \
php artisan migrate --force && \
php artisan optimize:clear && \
php artisan cache:clear && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
php artisan optimize && \
sudo systemctl restart php8.2-fpm && \
sudo systemctl restart nginx && \
echo "✅ Deployment complete! Verify at https://your-domain.com"
```

**Warning**: Only use this if you're confident. Always use step-by-step deployment for safety.

---

## Important Considerations

### Version Information

The version information is automatically generated by `build-version.sh`. Do not manually edit `config/version.php`.

To check current version:
```bash
php artisan tinker
> config('version')
```

### Database Backups

Always create a database backup before deployment:

```bash
# MySQL backup
mysqldump -u username -p database_name > backup-$(date +%Y%m%d-%H%M%S).sql

# PostgreSQL backup
pg_dump -U username database_name > backup-$(date +%Y%m%d-%H%M%S).sql
```

### Maintenance Mode

To minimize user impact during deployment:

```bash
# Enable maintenance mode
php artisan down --message='Maintenance in progress' --retry=60

# Deployment steps here...

# Disable maintenance mode
php artisan up
```

### Large File Uploads

If deploying large assets or database dumps:

```bash
# Increase upload and timeout limits
php -r "echo ini_get('upload_max_filesize');"
php -r "echo ini_get('max_execution_time');"
```

---

## Environment-Specific Notes

### Production Deployment

For production environments:

- Always backup before deployment
- Deploy during maintenance window
- Monitor application for 1 hour after deployment
- Have rollback procedure ready
- Document deployment in changelog
- Notify relevant teams

### Staging Deployment

For staging/testing environments:

- Can deploy at any time
- Use for testing changes before production
- Document any issues found
- Test upgrade path thoroughly

### Local Development

For local development machines:

```bash
# Use development dependencies
composer install

# Build assets with source maps for debugging
npm run development

# Skip --force flag for migrations (manual confirmation)
php artisan migrate

# Optional: Use Laravel Valet or similar
valet serve
```

---

## Support and Help

### Helpful Commands

```bash
# Check current branch
git branch

# Check recent commits
git log --oneline -5

# Check application status
php artisan status

# Check queue jobs
php artisan queue:failed

# Restart queue workers (if using background jobs)
php artisan queue:restart

# View error logs
tail -f storage/logs/laravel.log

# Check disk space
df -h

# Check PHP version
php -v
```

### Log Files

Application logs are located in:
```
storage/logs/laravel.log
```

View real-time logs:
```bash
tail -f storage/logs/laravel.log
```

### Getting Help

If you encounter issues:

1. Check the **Troubleshooting** section above
2. Review Laravel logs in `storage/logs/`
3. Check nginx/apache error logs
4. Verify `.env` configuration is correct
5. Ensure all prerequisites are installed
6. Contact: Repository maintainer or team lead

---

## Version History

| Branch | Status | Purpose |
|--------|--------|---------|
| feature/custom-bast-module | Active | Custom module development |
| master | Stable | Production-ready releases |
| develop | Testing | Integration and testing |

To check current version info:
```bash
php artisan tinker
> config('version')
```

---

## Additional Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Snipe-IT Docs**: https://snipe-it.readme.io
- **Repository**: https://github.com/akhr0m/snipe-it
- **Issues**: https://github.com/akhr0m/snipe-it/issues

---

**Last Updated**: June 2026  
**Document Version**: 1.0