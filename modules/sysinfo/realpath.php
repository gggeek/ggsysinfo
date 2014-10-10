<?php

$tpl->setVariable(
    'realpath_cache',
    array(
        'available' => ini_get( 'realpath_cache_size' ),
        'ttl' => ini_get( 'realpath_cache_ttl' ),
        'used' => realpath_cache_size()
    )
);