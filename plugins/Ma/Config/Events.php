<?php

namespace Ma\Config;

use CodeIgniter\Events\Events;

Events::on('pre_system', function () {
    helper("ma");
    helper("ma_general");
});