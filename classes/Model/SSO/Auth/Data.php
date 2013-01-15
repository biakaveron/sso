<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_SSO_Auth_Data extends ORM {

	protected $_table_name = 'auth_data';

	/**
	 * Get avatar URL
	 *
	 * @param  int $size  used for gravatar images only
	 *
	 * @return mixed|null|string
	 */
	public function get_avatar($size = NULL)
	{
		$avatar = $this->avatar;
		if (empty($avatar) AND ! empty($this->email) )
		{
			// use email as Gravatar ID
			$avatar = md5($this->email);
		}

		if (empty($avatar))
		{
			return NULL;
		}

		if (strpos($avatar, '://') == FALSE)
		{
			// its a Gravatar ID
			$avatar = 'http://gravatar.com/avatar/' . $avatar;
			$params = array();
			if (empty($avatar))
			{
				// use default Gravatar
				$params['f'] = 'y';
			}

			if ($size)
			{
				$params['s'] = intval($size);
			}

			if ( ! empty($params) )
			{
				$avatar .= http_build_query($params);
			}
		}

		return $avatar;
	}

}