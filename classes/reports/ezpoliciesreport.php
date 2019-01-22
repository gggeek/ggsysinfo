<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2019
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 */

class ezPoliciesReport implements ezSysinfoReport
{

    public function getReport()
    {
        // quick and dirty: use same data as for the web
        $tpl = sysInfoTools::eZTemplateFactory();
        $tpl->setVariable( 'title', 'Roles & Policies Report' );
        $tpl->setVariable( 'roles', self::getRoles() );
        $htmlReport = $tpl->fetch( "design:sysinfo/policiesreport.tpl" );
        return $htmlReport;
    }

    public function getDescription()
    {
        return array(
            'tag' => 'rolesandpolicies',
            'title' => 'Roles & Policies Report',
            'executingString' => 'Gathering roles & policies definition...',
            'format' => 'html'
        );
    }

    /**
     * We do our best to sort in a way that makes matching easy when ids are different
     * @return array
     *
     * @todo finish obj to array conversion ($users) to make it easier to have non-html output
     * @todo sorting of same-policies-w.-different-limitations in a role is far from perfect despite our efforts
     */
    public static function getRoles()
    {
        $eZroles = eZRole::fetchByOffset( 0, false, true, true );
        $roles = array();

        // scrap original array, create a new one where policies are sorted. Can you follow the logic?
        foreach( $eZroles as $role )
        {
            $policies = array();
            $users = array();
            foreach( $role->attribute('policies') as $policy )
            {
                $limitations = array();
                foreach( $policy->attribute( 'limitations' ) as $limitation )
                {
                    $values = $limitation->attribute( 'values_as_array_with_names' );
                    // We only use the "name" in each limitation.
                    // This might cause fake-positives when comparing, f.e. different node-limitations on different folders all having the same name
                    // But comparing what is inside the limitation is hard (eg node-ids, which we do not want to compare)
                    $valNames = array();
                    foreach( $values as $item )
                    {
                        $valNames[] = $item["Name"];
                    }
                    $limName = $limitation->attribute( 'identifier' ) . '_' . md5( serialize( $valNames ) );
                    $limitations[$limName] = array(
                        'identifier' => $limitation->attribute( 'identifier' ),
                        'values_as_array_with_names' => $values
                    );
                }
                ksort( $limitations );
                $policy = array(
                    'module_name' => $policy->attribute('module_name'),
                    'function_name' => $policy->attribute('function_name'),
                    'limitations' => array_values( $limitations )
                );
                $policies[$policy['module_name'] . '_' . $policy['function_name'] . '_' . md5( serialize( array_keys( $limitations ) ) ) ] = $policy;
            }
            ksort( $policies );
            foreach( $role->fetchUserByRole() as $user )
            {
                $users[$user['user_object']->attribute('name')] = $user;
            }
            ksort( $users );
            $roles[$role->attribute('name')] = array(
                'name' => $role->attribute('name'),
                'policies' => $policies,
                'user_array' => $users );
        }

        ksort( $roles );
        return array_values( $roles );
    }
}