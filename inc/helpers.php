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
			'dir'           => "./database/member_data",
			'format'        => \Filebase\Format\Json::class,
			'cache'         => true,
			'cache_expires' => 1800,
			'pretty'        => false,
			'safe_filename' => true,
			'read_only'     => false
		]);

		if (!$db->has($id)) {
			$mem = $db->get($id);
			$data = json_decode(file_get_contents("https://api.gamebanana.com/Core/Item/Data?itemtype=Member&itemid={$id}&fields=name,Url().sGetUpicUrl(),Url().sGetAvatarUrl()"), true);

			if (array_key_exists('error', $data)) throw new \Exception($data['error']);

			$username = $data[0];
			//	Would this work using null coalescing instead? Using this syntax for now...
			$upic     = !$data[1] ? null : $data[1];
			$avatar   = $data[2];

			$mem->id = $id;
			$mem->username = $username;
			$mem->upic = $upic;
			$mem->avatar = $avatar;
			$mem->save();
		} else {
			// Update existing member's avatar and upic after 1 week
			$mem = $db->get($id);
			if (($mem->updatedAt(false) + 604800) <= time()) {
				$data = json_decode(file_get_contents("https://api.gamebanana.com/Core/Item/Data?itemtype=Member&itemid={$id}&fields=Url().sGetUpicUrl(),Url().sGetAvatarUrl()"), true);

				if (array_key_exists('error', $data)) throw new \Exception($data['error']);

				$upic   = !$data[0] ? null : $data[0];
				$avatar = $data[1];

				$mem->upic = $upic;
				$mem->avatar = $avatar;
				$mem->save();
			}
		}

		switch ($field) {
			default:
			case 'id':
				$return = $id;
				break;
			case 'username':
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

		$left  = user_info($owner, 'upic') !== null ? '<img src="'.user_info($owner, 'upic').'" alt="'.$left_username.'">' : $left_username ;
		$right  = user_info($visitor, 'upic') !== null ? '<img src="'.user_info($visitor, 'upic').'" alt="'.$right_username.'">' : $right_username ;

		return is_owner() ? 'Yourself' : $left . ' &times; ' . $right;
	}

	function members_list(int $owner) : string {
		$members = new \Filebase\Database([
			'dir'            => "./database/member_$owner",
			'format'         => \Filebase\Format\Json::class,
			'cache'          => true,
			'cache_expires'  => 1800,
			'pretty'         => false,
			'safe_filename'  => true,
			'read_only'      => true
		]);

		if ($members->count() > 0) {
			$table_rows = [];
			$mems = $members->findAll();
			foreach ($mems as $m) {
				if ($m->id > 0) {
					// TODO: Redo these 6 lines below...
					$_avatar = user_info($m->id, 'avatar');
					$_upic = user_info($m->id, 'upic');
					$_username = user_info($m->id, 'username');
					$avatar = '<a href="https://gamebanana.com/members/'.$m->id.'" class="Avatar" style="margin-right:0.5rem;"><img src="'.$_avatar.'" alt="'.$_username.'" title="'.$_username.'" style="width:25px;height:25px;"></a>';
					$upic = $_upic !== null ? '<img src="'.$_upic.'" alt="'.$_username.'" title="'.$_username.'">' : null;
					$username = '<a href="https://gamebanana.com/members/'.$m->id.'">'.($upic ?? $_username).'</a>';

					$tr = "<tr><td>$avatar $username</td><td>{$m->value}%</td><td>{$m->createdAt()}</td></tr>";
					$table_rows[] = $tr;
				}
			}

			$joined_rows = implode('', $table_rows);

			return "<table style=\"padding:1rem;width:100%;\"><thead><tr><th>Member</th><th>Love</th><th>Date</th></tr></thead><tbody>$joined_rows</tbody></table>";
		} else {
			return '<ul class="LogMessages"><li class="RedColor">No member data available.</li></ul>';
		}
	}