<?php
    function check_internat(){
        if(!$sock = @fsockopen('www.google.com', 80))
        {
            header("location: no_connection.php");
        }
    }
?>