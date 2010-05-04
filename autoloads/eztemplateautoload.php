<?php

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/ggsysinfo/autoloads/ggsysinfotemplateoperators.php',
                                    'class' => 'ggSysinfoTemplateOperators',
                                    'operator_names' => array_keys( ggSysinfoTemplateOperators::$operators ) );


?>
