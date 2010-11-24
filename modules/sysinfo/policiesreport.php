<?php

$roles = eZRole::fetchByOffset( 0, false, true, true );

// scrap original array, create a new one where policies are sorted
foreach( $roles as $i => $role )
{
    $policies = array();
    $users = array();
    foreach( $role->attribute('policies') as $policy )
    {
        $policies[$policy->attribute('module_name') . '_' . $policy->attribute('function_name')] = $policy;
    }
    ksort( $policies );
    foreach( $role->fetchUserByRole() as $user )
    {
        $users[$user['user_object']->attribute('name')] = $user;
    }
    ksort( $users );
    $roles[$i] = array(
        'name' => $role->attribute('name'),
        'policies' => $policies,
        'user_array' => $users );
}

$tpl->setVariable( 'roles', $roles );

?>