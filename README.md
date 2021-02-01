# AdminPHP

This Will help create an admin managment platform.

the hierarchy:

all this needs to be in a server on this path:
/var/www/html/

So we have:
/var/www/html/admin/
/var/www/html/crons/
/var/www/html/general/


the DBCreate folder contains a sql script that builds all the tables for the basic use, empty.

In order to create a new module:
1. Create a table in the DB
2. create a folder under /var/www/html/admin/  with the table name
3. Add a cfg like in the example_moudle.
4. Add a menu item in the menu_items table
5. Add privileges to the users
