<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_SSO_Auth_Data extends ORM {

	protected $_table_name = 'auth_data';

	protected $_belongs_to = array(
		'user' => array(),
	);

}