# Love Calculator

This is the source code for the [Love Calculator module on Gamebanana.com.](https://gamebanana.com/apps/519)

It works by taking the profile and visitor IDs into account when generating a seed for the number generator.

---

This module uses only one Composer library called [Filebase.](https://github.com/filebase/Filebase) It is used for caching user info for later use and storing love calculations for profiles.

It was developed on, tested on, and runs on PHP 7.3.

As of 6/11/2019, the module will only function with a valid key. To test this with the cloned repo, create an **env.php** file and fill it with
```php
<?php

	define('KEY', 'YOUR SECRET KEY HERE');
```

Then, access it with `/path/to/module/index.php/<KEY>`