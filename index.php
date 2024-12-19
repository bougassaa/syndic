<?php
// Redirect all requests to the public directory
header('Location: /public' . $_SERVER['REQUEST_URI']);
exit();