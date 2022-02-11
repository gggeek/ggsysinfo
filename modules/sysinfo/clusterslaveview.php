<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * A view which is used to do alternative permission checking, then execute one of the std views
 */

$module = $Params['Module'];
$targetView = array_shift( $Params['Parameters'] );
$token = $Params['authToken'];

// check intra-cluster token-based auth
if ( !ezSysinfoClusterManager::verifyAuthToken( $targetView, $token ) )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

// signal to genericview.php that auth is ok
ezSysinfoClusterManager::setAuthStatus( true );

// nb: we have to take care not execute clustermaster.php again, which makes view definition too complex to be good
$Result = $module->run( $targetView, array_merge( $Params['Parameters'], $Params['UserParameters'] ) );
