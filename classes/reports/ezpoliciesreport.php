<?php
/**
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2020
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
                    $limValues = array();
                    foreach( $values as $item )
                    {
                        $limValues[] = $item["Name"];
                    }
                    sort($limValues);
                    $limitations[] = array(
                        'identifier' => $limitation->attribute( 'identifier' ),
                        'values_as_array_with_names' => $limValues
                    );
                }
                // sort based on limitation identifier, then elements of lim. values
                usort( $limitations, array( 'ezPoliciesReport', 'compareLimitations' ) );

                $policy = array(
                    'module_name' => $policy->attribute('module_name'),
                    'function_name' => $policy->attribute('function_name'),
                    'limitations' => $limitations
                );
                $policies[] = $policy;
            }

            usort( $policies, array( 'ezPoliciesReport', 'comparePolicies' ) );
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

    /**
     * @param array $limA
     * @param array $limB
     * @return int
     */
    protected static function compareLimitations( $limA, $limB )
    {
        if ( $limA['identifier'] != $limB['identifier'] )
        {
            return strcmp( $limA['identifier'], $limB['identifier'] );
        }
        else
        {
            foreach( $limA['values_as_array_with_names'] as $key => $val )
            {
                if ( !isset( $limB['values_as_array_with_names'][$key]) )
                {
                    // B has less elements => goes first
                    return 1;
                }
                $diff = strcmp( $val, $limB['values_as_array_with_names'][$key] );
                if ( $diff != 0 )
                {
                    return $diff;
                }
            }
            // A has less elements than B or equal
            return -1;
        }
    }

    protected static function comparePolicies( $polA, $polB )
    {
        if ( $polA['module_name'] != $polB['module_name'] )
        {
            return strcmp( $polA['module_name'], $polB['module_name'] );
        }
        elseif ( $polA['function_name'] != $polB['function_name'] )
        {
            return strcmp( $polA['function_name'], $polB['function_name'] );
        }
        else
        {
            foreach( $polA['limitations'] as $key => $limA )
            {
                if ( !isset( $polB['limitations'][$key]) )
                {
                    // B has less limitations => goes first
                    return 1;
                }

                $diff = self::compareLimitations( $limA, $polB['limitations'][$key] );
                if ( $diff != 0 )
                {
                    return $diff;
                }
            }
            // A has less limitations than B or equal
            return -1;
        }
    }
}
