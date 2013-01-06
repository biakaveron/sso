<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @property Model_SSO_Auth_Data $primary_data
 */
class Model_SSO_User extends ORM {

	protected $_table_name = 'users';

	protected $_has_many   = array(
		'auth_data'    => array(),
		'tokens'       => array(),
	);

	protected $_belongs_to = array(
		'primary_data' => array(
			'model'       => 'auth_data',
		),
	);

	public function rules()
	{
		return array(
			'username'   => array(
				array('not_empty'),
				array(array($this, 'unique'), array('username', ':value')),
			),
			'email'      => array(
				array('email'),
				array(array($this, 'unique'), array('email', ':value')),
			)
		);

	}

	public function create_user(Model_Auth_Data $data)
	{
		$this->set('username', $data->service_name);
		$this->primary_data = $data;
		$this->save();
		$data->user = $this;
		$data->save();
	}

	public function unique_key($value)
	{
		return Valid::email($value) ? 'email' : 'username';
	}

	public function unique_key_exists($value, $field = NULL)
	{
		if ($field === NULL)
		{
			// Automatically determine field by looking at the value
			$field = $this->unique_key($value);
		}

		return (bool) DB::select(array('COUNT("*")', 'total_count'))
			->from($this->_table_name)
			->where($field, '=', $value)
			->where($this->_primary_key, '!=', $this->pk())
			->execute($this->_db)
			->get('total_count');
	}

	public function unique($field, $value)
	{
		$model = ORM::factory($this->object_name())
			->where($field, '=', $value)
			->find();

		if ($this->loaded())
		{
			return ( ! ($model->loaded() AND $model->pk() != $this->pk()));
		}

		return ( ! $model->loaded());
	}

	/**
	 * @param  Array  $list
	 *
	 * @return Database_Result
	 */
	public function unique_usernames($list)
	{
		$existing = DB::select('username')
			->from($this->_table_name)
			->where('username', 'NOT IN', $list)
			->execute($this->_db)
			->as_array(NULL, 'username');
		return array_diff($list, $existing);
	}

	public function delete_tokens()
	{
		if ( ! $this->loaded())
		{
			// do nothing
			return FALSE;
		}

		return DB::delete($this->_has_many['tokens']['tablename'])
			->where($this->_has_many['tokens']['foreign_key'], '=', $this->pk())
			->execute($this->_db);
	}

	public function get_avatar($size = NULL)
	{
		if ( ! $this->loaded() OR ! $this->primary_data->loaded() )
		{
			return FALSE;
		}

		// try to load avatar from primary account profile
		$avatar = $this->primary_data->avatar;
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