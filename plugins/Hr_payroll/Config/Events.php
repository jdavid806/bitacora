<?php

namespace Hr_payroll\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
	helper("hr_payroll_general");
	helper("hr_payroll_datatables");
	helper("hr_payroll_convert_field");
	helper("notifications_helper");
});