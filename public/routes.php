<?php
        $router->get('/', function() {
            header("HTTP/1.1 403 Unauthorized");
            echo "Unauthorized";
            die();
        });
        $router->post('/api/verify', 'Controllers/api.php');
?>