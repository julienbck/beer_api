#!/bin/bash

set -e

# Change www-data's uid & guid to be the same as directory in host or the configured one
sed -ie "s/`id -u www-data`:`id -g www-data`/`stat -c %u /var/www/bar-management`:`stat -c %g /var/www/bar-management`/g" /etc/passwd

chown www-data:www-data /var/www

if [ "$1" = "apache2-foreground" ]; then
    exec "$@"
fi

su www-data -s /bin/bash -c "$*"
