<?php

namespace Hr_profile\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
	helper("hr_profile_general");
	helper("hr_profile_datatables");
	helper("hr_profile_convert_field");
	helper("notifications_helper");
});