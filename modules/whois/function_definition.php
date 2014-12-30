<?php
/**
 * @package eabWhois
 * @author  Andy Caiger <acaiger@eab.co.uk>
 * @date    28 March 2014
 **/

$FunctionList = array();

$FunctionList['info'] = array(
	'name'             => 'info',
	'call_method'      => array(
		'class'  => 'eabWhois',
		'method' => 'getInfo'
	),
	'parameter_type'   => 'standard',
	'parameters'       => array(
		array(
			'name'     => 'domain',
			'type'     => 'string',
			'required' => true,
			'default'  => ''
		)
	)
);

?>
