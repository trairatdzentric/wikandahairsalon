# Wikanda Hair Salon - InfinityFree Deployment Guide

## à¸‚à¸±à¹‰à¸™à¸•à¸­à¸™à¸à¸²à¸£à¸­à¸±à¸›à¹‚à¸«à¸¥à¸”à¹‚à¸›à¸£à¹€à¸ˆà¸à¸•à¹Œà¸‚à¸¶à¹‰à¸™ InfinityFree

### 1. à¹€à¸•à¸£à¸µà¸¢à¸¡à¹„à¸Ÿà¸¥à¹Œà¸ªà¸³à¸«à¸£à¸±à¸šà¸­à¸±à¸›à¹‚à¸«à¸¥à¸”

#### 1.1 à¸ªà¸£à¹‰à¸²à¸‡à¹„à¸Ÿà¸¥à¹Œ ZIP à¸‚à¸­à¸‡à¹‚à¸›à¸£à¹€à¸ˆà¸à¸•à¹Œ

```bash
# à¹„à¸Ÿà¸¥à¹Œà¸—à¸µà¹ˆà¸•à¹‰à¸­à¸‡à¸£à¸§à¸¡:
- app/
- config/
- database/
- public/
- storage/ (à¸¢à¸à¹€à¸§à¹‰à¸™ logs/*)
- composer.json (à¸–à¹‰à¸²à¸¡à¸µ)
- .htaccess (à¸ªà¸³à¸«à¸£à¸±à¸š public/)
```

#### 1.2 à¹„à¸Ÿà¸¥à¹Œà¸—à¸µà¹ˆà¹„à¸¡à¹ˆà¸•à¹‰à¸­à¸‡à¸­à¸±à¸›à¹‚à¸«à¸¥à¸”

```
- data/ (à¹ƒà¸Šà¹‰à¹€à¸‰à¸žà¸²à¸° JSON mode)
- docs/
- *.md files (à¸¢à¸à¹€à¸§à¹‰à¸™ README.md)
- .git/
- node_modules/ (à¸–à¹‰à¸²à¸¡à¸µ)
```

### 2. à¸­à¸±à¸›à¹‚à¸«à¸¥à¸”à¸œà¹ˆà¸²à¸™ FTP

#### 2.1 à¸‚à¹‰à¸­à¸¡à¸¹à¸¥à¸à¸²à¸£à¹€à¸Šà¸·à¹ˆà¸­à¸¡à¸•à¹ˆà¸­ FTP

```
Host: ftpupload.net
Username: YOUR_DB_USERNAME
Password: YOUR_DB_PASSWORD
Port: 21
```

#### 2.2 à¹‚à¸„à¸£à¸‡à¸ªà¸£à¹‰à¸²à¸‡à¹„à¸Ÿà¸¥à¹Œà¸šà¸™ InfinityFree

```
/htdocs/
â”œâ”€â”€ app/
â”œâ”€â”€ config/
â”œâ”€â”€ database/
â”œâ”€â”€ storage/
â”‚   â”œâ”€â”€ logs/
â”‚   â””â”€â”€ uploads/
â”‚       â””â”€â”€ slips/
â”œâ”€â”€ .htaccess
â””â”€â”€ index.php  (à¸ˆà¸²à¸ public/index.php)
```

### 3. à¸•à¸±à¹‰à¸‡à¸„à¹ˆà¸² .htaccess

à¸ªà¸£à¹‰à¸²à¸‡à¹„à¸Ÿà¸¥à¹Œ `.htaccess` à¹ƒà¸™ `/htdocs/`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# PHP Settings
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300

# Protect sensitive files
<FilesMatch "^\.(env|json|lock|log)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Disable directory listing
Options -Indexes

# Protect config folder
RewriteRule ^config/ - [F,L]
RewriteRule ^app/ - [F,L]
```

### 4. à¸•à¸±à¹‰à¸‡à¸„à¹ˆà¸² public/index.php à¸ªà¸³à¸«à¸£à¸±à¸š InfinityFree

à¹à¸à¹‰à¹„à¸‚ `public/index.php`:

```php
<?php
/**
 * Wikanda Hair Salon - Public Entry Point (InfinityFree)
 */

// à¸à¸³à¸«à¸™à¸” BASE_PATH à¸ªà¸³à¸«à¸£à¸±à¸š InfinityFree
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Error reporting (à¸›à¸´à¸”à¹ƒà¸™ production)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/error.log');

// Session configuration
ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_samesite', 'Strict');

// Autoloader
require_once APP_PATH . '/Core/autoload.php';

// Start session
session_start();

// Load configuration
$config = require CONFIG_PATH . '/app.php';

// Initialize router
use App\Core\Router;

$router = new Router();

// Load routes
require CONFIG_PATH . '/routes.php';

// Dispatch request
$router->dispatch();
```

### 5. à¸•à¸±à¹‰à¸‡à¸„à¹ˆà¸² Storage Permissions

à¸•à¸±à¹‰à¸‡à¸„à¹ˆà¸² permissions à¸œà¹ˆà¸²à¸™ FTP à¸«à¸£à¸·à¸­ File Manager:

```bash
chmod 755 /htdocs/storage/
chmod 755 /htdocs/storage/logs/
chmod 755 /htdocs/storage/uploads/
chmod 755 /htdocs/storage/uploads/slips/
chmod 644 /htdocs/storage/logs/*.log
```

### 6. à¸—à¸”à¸ªà¸­à¸šà¸à¸²à¸£à¸—à¸³à¸‡à¸²à¸™

#### 6.1 à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸šà¸«à¸™à¹‰à¸²à¹€à¸§à¹‡à¸š

- URL: `http://wikanda-hair-salon.infinityfreeapp.com/`
- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸šà¸§à¹ˆà¸²à¹‚à¸«à¸¥à¸”à¹„à¸”à¹‰à¸›à¸à¸•à¸´

#### 6.2 à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸šà¸à¸²à¸£à¹€à¸Šà¸·à¹ˆà¸­à¸¡à¸•à¹ˆà¸­à¸à¸²à¸™à¸‚à¹‰à¸­à¸¡à¸¹à¸¥

- à¸¥à¹‡à¸­à¸à¸­à¸´à¸™à¹€à¸‚à¹‰à¸²à¸£à¸°à¸šà¸š
- à¸—à¸”à¸ªà¸­à¸šà¸à¸²à¸£à¸”à¸¹à¸‚à¹‰à¸­à¸¡à¸¹à¸¥

#### 6.3 à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸šà¸à¸²à¸£à¸­à¸±à¸›à¹‚à¸«à¸¥à¸”à¹„à¸Ÿà¸¥à¹Œ

- à¸—à¸”à¸ªà¸­à¸šà¸­à¸±à¸›à¹‚à¸«à¸¥à¸”à¸ªà¸¥à¸´à¸›à¸à¸²à¸£à¹‚à¸­à¸™à¹€à¸‡à¸´à¸™

### 7. à¸à¸²à¸£ Migration à¸šà¸™ InfinityFree

#### 7.1 à¸­à¸±à¸›à¹‚à¸«à¸¥à¸”à¹„à¸Ÿà¸¥à¹Œ migration

```
/htdocs/database/migrate_json_to_mysql.php
```

#### 7.2 à¸£à¸±à¸™ migration à¸œà¹ˆà¸²à¸™ browser

```
https://wikanda-hair-salon.infinityfreeapp.com/database/migrate_json_to_mysql.php
```

**à¸«à¸¡à¸²à¸¢à¹€à¸«à¸•à¸¸:** à¸„à¸§à¸£à¸¥à¸šà¹„à¸Ÿà¸¥à¹Œ migration à¸«à¸¥à¸±à¸‡à¸ˆà¸²à¸à¸£à¸±à¸™à¹€à¸ªà¸£à¹‡à¸ˆà¹à¸¥à¹‰à¸§

### 8. à¸à¸²à¸£à¸ªà¸¥à¸±à¸šà¸à¸¥à¸±à¸šà¹„à¸›à¹ƒà¸Šà¹‰ JSON (à¸–à¹‰à¸²à¸ˆà¸³à¹€à¸›à¹‡à¸™)

à¹à¸à¹‰à¹„à¸‚ `config/database.php`:

```php
return [
    'driver'   => 'json',  // à¹€à¸›à¸¥à¸µà¹ˆà¸¢à¸™à¸ˆà¸²à¸ 'mysql' à¹€à¸›à¹‡à¸™ 'json'
    // ... à¸„à¹ˆà¸²à¸­à¸·à¹ˆà¸™à¹†
];
```

### 9. à¸à¸²à¸£ Backup à¸à¸²à¸™à¸‚à¹‰à¸­à¸¡à¸¹à¸¥

#### 9.1 à¸œà¹ˆà¸²à¸™ phpMyAdmin

1. à¹€à¸‚à¹‰à¸² phpMyAdmin à¸‚à¸­à¸‡ InfinityFree
2. à¹€à¸¥à¸·à¸­à¸ database: `YOUR_DB_NAME`
3. à¸à¸” Export â†’ Quick â†’ Go

#### 9.2 à¸œà¹ˆà¸²à¸™ PHP Script

à¸ªà¸£à¹‰à¸²à¸‡à¹„à¸Ÿà¸¥à¹Œ `backup.php`:

```php
<?php
require_once __DIR__ . '/config/database.php';

$backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

$command = "mysqldump --host=" . DB_HOST . " --user=" . DB_USER . " --password=" . DB_PASS . " " . DB_NAME . " > " . $backupFile;

system($command);

echo "Backup created: $backupFile";
```

### 10. Troubleshooting

#### à¸›à¸±à¸à¸«à¸²: 500 Internal Server Error

- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸š `.htaccess` syntax
- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸š PHP version (à¸•à¹‰à¸­à¸‡à¹€à¸›à¹‡à¸™ 8.0+)
- à¸”à¸¹ error log à¹ƒà¸™ `/storage/logs/error.log`

#### à¸›à¸±à¸à¸«à¸²: Cannot connect to database

- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸šà¸„à¹ˆà¸² config à¹ƒà¸™ `config/database.php`
- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸šà¸§à¹ˆà¸² MySQL service à¸—à¸³à¸‡à¸²à¸™à¸›à¸à¸•à¸´
- à¸¥à¸­à¸‡à¹€à¸Šà¸·à¹ˆà¸­à¸¡à¸•à¹ˆà¸­à¸œà¹ˆà¸²à¸™ phpMyAdmin

#### à¸›à¸±à¸à¸«à¸²: File upload failed

- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸š permissions à¸‚à¸­à¸‡ `/storage/uploads/`
- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸š `upload_max_filesize` à¹ƒà¸™ PHP settings
- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸š disk quota à¸‚à¸­à¸‡ hosting

#### à¸›à¸±à¸à¸«à¸²: Session not working

- à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸šà¸§à¹ˆà¸² session path à¸¡à¸µ permissions à¸–à¸¹à¸à¸•à¹‰à¸­à¸‡
- à¸¥à¹‰à¸²à¸‡ browser cookies/cache

### 11. Security Checklist

- [ ] à¹€à¸›à¸¥à¸µà¹ˆà¸¢à¸™ default passwords
- [ ] à¸‹à¹ˆà¸­à¸™ `config/` à¹à¸¥à¸° `app/` à¸”à¹‰à¸§à¸¢ .htaccess
- [ ] à¸•à¸±à¹‰à¸‡à¸„à¹ˆà¸² HTTPS redirect
- [ ] à¸›à¸´à¸” error display à¹ƒà¸™ production
- [ ] à¹ƒà¸Šà¹‰ prepared statements à¸—à¸¸à¸ query
- [ ] à¸•à¸£à¸§à¸ˆà¸ªà¸­à¸š file upload types
- [ ] à¹ƒà¸Šà¹‰ CSRF tokens à¹ƒà¸™à¸Ÿà¸­à¸£à¹Œà¸¡

### 12. Contact & Support

- InfinityFree Support: https://forum.infinityfree.net/
- phpMyAdmin: https://YOUR_PHPMYADMIN_HOST/
- FTP Client: FileZilla (à¹à¸™à¸°à¸™à¸³)

---

**à¸«à¸¡à¸²à¸¢à¹€à¸«à¸•à¸¸:** à¹€à¸­à¸à¸ªà¸²à¸£à¸™à¸µà¹‰à¸­à¸±à¸›à¹€à¸”à¸•à¸¥à¹ˆà¸²à¸ªà¸¸à¸”: 2026-06-06

