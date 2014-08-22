<?php
    $config = new Doctrine\DBAL\Configuration();
    $connectionParams = array(
            'dbname' => 'ips_db',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        
            'tbl_name' => 'ips_tbl',
        );
    $connection = Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    
?>
