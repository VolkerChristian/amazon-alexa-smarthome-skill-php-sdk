<?php

    $connection = ssh2_connect('werama.home.vchrist.at', 22);
    ssh2_auth_pubkey_file($connection, 'pi', 
        '/home/voc/.ssh/id_rsa.pub',
        '/home/voc/.ssh/id_rsa', 
        '');

    /* if you want to execute script from a different directory then use commands in same line separated 
        by ';', that is required in php.
        In below second command '&' will do the magic to run command forever.
    */
    if ( $connection == FALSE ) {
        echo "False \n";
    } else {
        echo "Not False \n";
    }
    
    $stream = ssh2_exec($connection, 'cd httpdocs/subdir/dir; /path/to/php server.php &'); 

    echo "Stream: " . $stream . "\n";
    $stream = ssh2_exec($connection, 'ps aux | grep server.php'); 

    // printing ssh output on screen
    stream_set_blocking($stream, true);
    $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
    echo stream_get_contents($stream_out); 
    // closing ssh connection
    $stream = ssh2_exec($connection, 'exit'); 
    unset($connection);

?>
