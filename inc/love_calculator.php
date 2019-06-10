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

	/**
	 * TODO: Rewrite using a switch statement
	*/
	function love_message($per) : string {
		if($per <= 0)
		{
			return "<i>Uh oh!</i> Maybe you two should see other people";
		}
		elseif($per >= 10 AND $per < 20)
		{
			return "Awful&hellip; 😭";
		}
		elseif($per >= 20 AND $per < 40)
		{
			return "Not too great";
		}
		elseif($per >= 40 AND $per < 50)
		{
			return "Worse than average";
		}
		elseif($per == 50)
		{
			return "There could be a chance";
		}
		elseif($per > 50 AND $per < 75)
		{
			return "Not bad!";
		}
		elseif($per === 69)
		{
			return "<strong>( ͡° ͜ʖ ͡°)</strong>";
		}
		elseif($per >= 75 AND $per < 90)
		{
			return "Pretty good!";
		}
		elseif($per >= 90 AND $per < 99)
		{
			return "Great! 😄";
		}
		elseif($per >= 100)
		{
			return "A perfect match! 😍";
		}
	}

	function love_message_self($per) : string {
		if($per <= 10)
		{
			return "<i>Uh oh!</i> Maybe you should seek a therapist";
		}
		elseif($per <= 50)
		{
			return "Your views of yourself <em>could</em> be better";
		}
		elseif($per > 50 AND $per < 90)
		{
			return "Glad to see you love yourself quite well";
		}
		elseif($per >= 90)
		{
			return "You are narcissistic";
		}
	}
