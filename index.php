<!DOCTYPE html>
<html>
    <head>
        <title>My PHP Page</title>
    </head>
    <body>
        <?php
            echo "<h1>La version PHP utilisée est la " . phpversion() . "</h1>";
            // Ou plus directement: phpinfo();
        ?>
    </body>
</html>