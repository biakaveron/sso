<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_SSO_ORM implements Interface_SSO_ORM {

	/**
	 * @param  mixed  $data  user data (Array) or user ID (int) or ORM object
	 * @return Model_Auth_Data
	 */
	public function get_user($data)
	{
		if ($data instanceof ORM)
		{
			// refresh user info
			$data = $data->pk();
		}

		if ( ! is_array($data) )
		{
			// find by unique key
			$user = ORM::factory('auth_data', $data);
		}
		else
		{
			// get user by service identity
			$user = ORM::factory('auth_data')
				->where('service_id', '=', $data['service_id'])
				->where('service_type', '=', $data['service_type'])
				->find();

			if ($user->loaded())
			{
				return $user;
			}
			// user not found
			return $this->_save_user($data);
		}

		return $user->loaded() ? $user : FALSE;
	}

	/**
	 * @param  $token
	 *
	 * @return  Model_Token|bool
	 */
	public function get_token($token)
	{
		$token = ORM::factory('sso_token')->where('token', '=', $token)->find();
		if ($token->is_valid())
		{
			return $token;
		}
		else
		{
			$this->delete_token($token);
			return FALSE;
		}
	}

	public function generate_token($user, $driver, $lifetime = NULL)
	{
		$token = ORM::factory('token');
		$token->generate($lifetime);
		$token->user = $user;
		$token->driver = $driver;
		$token->save();
		return $token;
	}

	public function delete_token($token)
	{
		if ( ! $token)
		{
			return FALSE;
		}

		if ( ! is_object($token))
		{
			$token = ORM::factory('token')->where('token', '=', $token)->find();
		}

		if ($token->loaded())
		{
			return $token->delete();
		}

		return FALSE;
	}

	protected function _save_user(array $data)
	{
		$auth_data = ORM::factory('auth_data')
			->set('service_id', $data['service_id'])
			->set('service_type', $data['service_type'])
			->set('service_name', $data['service_name'])
			->set('email', $data['email'])
			->set('avatar', Arr::get($data, 'avatar'))
			->set('is_active', (bool)Kohana::$config->load('sso.active_user'));
		$auth_data->save();

		return $auth_data;
	}
}

