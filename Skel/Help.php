# How to create .skel files and use them #

First create a 'skel' file:

path/to/file.php.skel:

>   $db_name = '<DATABASE_NAME>';
>   $db_user = '<DATABASE_USER>';
>   $db_pass = '<DATABASE_PASS>';

Now, create run:

#   phake skel vars

This will show you the vars that phake found:

>   Found markers in: path/to/file.php.skel:
>    - <DATABASE_NAME>
>    - <DATABASE_USER>
>    - <DATABASE_PASS>

To generate a file called "file.php" from the "file.php.skel" file, just run:

#   phake skel parse

That's it.

