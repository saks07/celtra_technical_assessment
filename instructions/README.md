# Setup

1. Server: Apache 2.4.41
2. Language: PHP 7.4.0
3. Database: MySQL MariaDB 10.4.10
4. Composer PHP dependency manager
5. Wamp server 64 for windows
6. Testing: Postman REST API client

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

## API endpoints

### Notes

* CREATE: [POST] /api/notes
* READ: [GET] /api/notes/:id?
* UPDATE: [POST, PUT] /api/notes/:id
* DELETE: [DELETE] /api/notes/:id

### Folders

* CREATE: [POST] /api/folders
* READ: [GET] /api/folders/:id?
* UPDATE: [POST, PUT] /api/folders/:id
* DELETE: [DELETE] /api/folders/:id

# IMPORTANT!

For testing update endpoints, I had to use the POST request method, with additional request headers, used to override the method in the API, instead of PUT, I did not find out why exactly, but based on the specification of the PHP framework ( https://www.slimframework.com/ ) used for the API, my guess was that the framework was the issue, because the specification had some suggestions for using PUT requests.
