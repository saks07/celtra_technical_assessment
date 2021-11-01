# Setup

1. Server: Apache 2.4.41
2. Language: PHP 7.4.0
3. Database: MySQL MariaDB 10.4.10
4. Composer PHP dependency manager
5. Wamp server 64 for windows

## Apache vhosts setup

```
<VirtualHost *:80>
  ServerName notesapi
  ServerAlias notesapi.ddev
  DocumentRoot "${INSTALL_DIR}/www/notes_api/public"
  <Directory "${INSTALL_DIR}/www/notes_api/public">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require local
    Require all granted
  </Directory>
</VirtualHost>
```

## Windows host file

```
127.0.0.1 notesapi.ddev
::1 notesapi
```

