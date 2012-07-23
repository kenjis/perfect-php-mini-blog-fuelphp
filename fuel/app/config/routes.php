<?php
return array(
	'_root_'  => 'status/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	//'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

	'user/:user_name/status/:id' => 'status/show',
	'user/:user_name'            => 'status/user',
	'follow'                     => 'account/follow',
);