<?php
set_last_visit($_SESSION['id']);
session_destroy();
redir("./");
?>