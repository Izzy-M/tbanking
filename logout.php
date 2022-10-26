<?php
session_start();
if(isset($_SESSION['csysuser'])){
    unset($_SESSION['csysuser']);
   
}
 echo '<script>location.replace("index.php")</script>';

?>