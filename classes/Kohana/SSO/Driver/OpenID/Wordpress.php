<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_SSO_Driver_OpenID_Wordpress extends SSO_Driver_OpenID {

	public $name = 'OpenID.Wordpress';

	protected function _get_user_data($user)
	{
		$result = parent::_get_user_data($user);

		// Wordpress login looks like http://username.wordpress.com
		$username = trim(str_replace('http://', '', $result['service_name']), '/');
		$result['service_name'] = current(explode('.', $username));

		return $result;
	}

}