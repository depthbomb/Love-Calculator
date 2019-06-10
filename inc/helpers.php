<?php

	/**
	 * Stores GB user info so we don't need to use the API each time
	 * TODO: Add a way to update old info
	 * 
	 * @param int $id
	 * @param string $field
	 * @return string
	 */
	function user_info(int $id, string $field) : ?string {
		$return = '';
		$db = new \Filebase\Database([
			'dir'            => "./database/member_data",
			'format'         => \Filebase\Format\Json::class,
			'cache'          => true,
			'cache_expires'  => 1800,
			'pretty'         => false,
			'safe_filename'  => true,
			'read_only'      => false
		]);

		if (!$db->has($id)) {
			$mem = $db->get($id);
			$data = json_decode(file_get_contents("https://api.gamebanana.com/Core/Item/Data?itemtype=Member&itemid={$id}&fields=name,Url().sGetUpicUrl(),Url().sGetAvatarUrl()"), true);

			if (array_key_exists('error', $data)) throw new \Exception($data['error']);

			$username = $data[0];
			//	Would this work using null coalescing instead? Using this syntax for now...
			$upic     = !$data[1] ? null : $data[1];
			$avatar   = $data[2];

			$mem->username = $username;
			$mem->upic = $upic;
			$mem->avatar = $avatar;
			$mem->save();
		}

		switch ($field) {
			case 'username':
			default:
				$return = $db->get($id)->username;
				break;
			case 'upic':
				$return = $db->get($id)->upic;
				break;
			case 'avatar':
				$return = $db->get($id)->avatar;
				break;
		}

		return $return;
	}

	/**
	 * Checks if the person viewing a profile is the owner
	 * 
	 * @return boolean
	 */
	function is_owner() : bool {
		$owner   = $_GET['_idProfile'] ?? '';
		$visitor = $_GET['_idMember'] ?? '';

		return (
			$owner !== '' &&
			$visitor !== '' &&
			$owner === $visitor
		);
	}

	function headline(int $owner, int $visitor) : string {
		$left_username = htmlspecialchars(user_info($owner, 'username'));
		$right_username = htmlspecialchars(user_info($visitor, 'username'));
		$left = '<img src="'.user_info($owner, 'upic').'" alt="'.$left_username.'">' ?? $left_username;
		$right = '<img src="'.user_info($visitor, 'upic').'" alt="'.$right_username.'">' ?? $right_username;

		return is_owner() ? 'You & Yourself' : $left . ' &times; ' . $right;
	}