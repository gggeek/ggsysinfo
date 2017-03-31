<?php

$tpl->setVariable(
    'realpath_cache',
    array(
        'available' => ini_get( 'realpath_cache_size' ),
        'used' => realpath_cache_size(),
        'entries' => count( realpath_cache_get() ),
        'ttl' => ini_get( 'realpath_cache_ttl' )
    )
);
