<?php

namespace Recruitment\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
	helper("recruitment_general");
	helper("recruitment_datatables");
	helper("recruitment_convert_field");
	helper("notifications_helper");
});