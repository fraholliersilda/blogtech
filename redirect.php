<?php

// Redirect to specified URL after 0.3 seconds
function redirect($url)
{
    header('Refresh: 0.3; url=' . $url);
}