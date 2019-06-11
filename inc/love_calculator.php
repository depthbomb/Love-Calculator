<?php

	function love(int $owner, int $visitor) : int {
		if ($owner === null || $visitor === null) die('Invalid owner or visitor ID');

		//	Gotta make sure it is unique!
		$seed = srand($owner+$visitor*($owner+$visitor)+$visitor);
		$love = rand(0, 101);
		srand();	//	Reset seeding just in case...
		
		$db = new \Filebase\Database([
			'dir'           => "./database/member_$owner",
			'format'        => \Filebase\Format\Json::class,
			'cache'         => true,
			'cache_expires' => 1800,
			'pretty'        => false,
			'safe_filename' => true,
			'read_only'     => false
		]);

		if (!$db->has($visitor)) {
			$v = $db->get($visitor);
			$v->id = $visitor;
			$v->value = $love;
			$v->save();
		}

		return $love;
	}

	function love_message(int $per) : string {
		switch (true) {
			case $per < 10:
				return '<i>Uh oh!</i> Maybe you two should see other people.';
				break;
			case in_array($per, range(10, 19)):
				return 'Awful&hellip; 😭';
				break;
			case in_array($per, range(20, 39)):
				return 'Not too great&hellip;';
				break;
			case in_array($per, range(40, 49)):
				return 'Worse than average.';
				break;
			case in_array($per, range(50, 68)):
				return 'There could be a chance';
				break;
			case $per === 69:
				return '<strong>( ͡° ͜ʖ ͡°)</strong>';
				break;
			case in_array($per, range(70, 89)):
				return 'Pretty good!';
				break;
			case in_array($per, range(90, 99)):
				return 'Great! 😄';
				break;
			case $per > 99:
				return 'A perfect match! 😍';
				break;
		}
	}

	function love_message_self(int $per) : string {
		switch (true) {
			case $per <= 10:
				return '<i>Uh oh!</i> Maybe you should seek a therapist.';
				break;
			case $per < 50:
				return 'Your views of yourself <em>could</em> be better';
				break;
			case in_array($per, range(50, 90)):
				return 'Glad to see you love yourself quite well';
				break;
			case $per > 90:
				return 'You are narcissistic';
				break;
		}
	}
