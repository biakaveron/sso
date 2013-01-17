<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_SSO_Driver_OpenID_Yahoo extends SSO_Driver_OpenID {

	public $name = 'OpenID.Yahoo';

	protected function _get_user_data($user)
	{
		$result = parent::_get_user_data($user);

		if ( ! empty($result['email']))
		{
			// Yahoo returns contact/email field - get username from its
			$result['service_name']   = current(explode('@', $result['email']));
		}

		return $result;
	}
}