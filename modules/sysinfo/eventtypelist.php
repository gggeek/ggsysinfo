<?php
/**
 * List all existing workflow event types
 *
 * @author G. Giunta
 * @copyright (C) G. Giunta 2014-2022
 * @license Licensed under GNU General Public License v2.0. See file license.txt
 *
 * @todo allow filtering by extension
 * @todo add information: originating extension for each type
 */

/** @var array $Params */
/** @var eZTemplate $tpl */
/** @var eZINI $ini */

$eventTypeList = eZWorkflowType::fetchRegisteredTypes();
ksort( $eventTypeList );

$workflows = array();
$extensions = eZModuleLister::getModuleList(); // ...
if ( $Params['extensionname'] != '' && !array_key_exists( $Params['extensionname'], $extensions ) )
{
    /// @todo
}
else
{
    foreach( $eventTypeList as $typeString => $type )
    {
        $workflows[$typeString] = array();
        $filter = array( 'workflow_type_string' => $typeString );
        $events = eZWorkflowEvent::fetchFilteredList( $filter );
        foreach( $events as $event )
        {
            $workflowId = $event->attribute( 'workflow_id' );

            if ( isset( $workflows[$typeString][$workflowId] ) )
            {
                $workflows[$typeString][$workflowId]['events'][] = $event;
            }
            else
            {
                $workflow = eZWorkflow::fetch( $workflowId );
                $workflows[$typeString][$workflowId] = array(
                    'workflow' => $workflow,
                    'events' => array( $event )
                );
            }
            $workflowEvents[$typeString][$event->attribute( 'id' )] = $event;
        }
    }
}

$title = 'List of available workflow event types';
if ( $Params['extensionname'] != '' )
{
    $title .= ' in extension "' . $Params['extensionname'] . '"';
    $extra_path = $Params['extensionname'];
}

$ezgeshi_available = sysInfoTools::ezgeshiAvailable();

$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'eventtypelist', $eventTypeList );
$tpl->setVariable( 'workflows', $workflows );
$tpl->setVariable( 'sdkversion', eZPublishSDK::version() );
$tpl->setVariable( 'ezgeshi_available', $ezgeshi_available );
$tpl->setVariable( 'source_available', sysInfoTools::sourceCodeAvailable( eZPublishSDK::version() ) );
