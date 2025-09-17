<script type="text/javascript">
      var site_url = $('input[name="site_url"]').val();
  var admin_url  = $('input[name="site_url"]').val();

    var fnServerParams = {};

    (function($) {
    	"use strict";
        
        $(".select2").select2();
        initColorPicker();

        $.each($('._hidden_inputs._filters input'),function(){
            fnServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });

        
        init_stage_manage();

        $('.add-new-stage').on('click', function(){

          $('#stage-modal').find('button[type="submit"]').prop('disabled', false);
          $('#stage-modal').modal('show');
          $('input[name="id"]').val('');
          $('input[name="name"]').val('');
          $('input[name="weight"]').val('');
          $('input[name="color"]').val('');
          $('textarea[name="description"]').val('');
        });

        $("body").on('change', '.onoffswitch input', function (event, state) {
            "use strict";

            var $selector = $(this),
            switch_url = $selector.attr('data-switch-url');
            if (!switch_url) {
                return;
            }

            switch_field(this);
        });

    })(jQuery);

    function edit_stage(id) {
      "use strict";
        $('#stage-modal').find('button[type="submit"]').prop('disabled', false);

      requestGetJSON(admin_url + 'ma/get_data_stage/'+id).done(function(response) {
          $('select[name="category"]').val(response.category).change();
          $('input[name="name"]').val(response.name);
          $('input[name="weight"]').val(response.weight);
          $('input[name="color"]').val(response.color);
          $('input[name="id"]').val(id);
            $('textarea[name="description"]').val(response.description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " "));

          $('#stage-modal').modal('show');

      });
    }

    // custom view will fill input with the value
    function dt_stage_custom_view(value, table, custom_input_name, clear_other_filters) {
    "use strict";
        var name = typeof (custom_input_name) == 'undefined' ? 'custom_view' : custom_input_name;
        if (typeof (clear_other_filters) != 'undefined') {
            var filters = $('._filter_data li.active').not('.clear-all-prevent');
            filters.removeClass('active');
            $.each(filters, function () {
                var input_name = $(this).find('a').attr('data-cview');
                $('._filters input[name="' + input_name + '"]').val('');
            });
        }
        var _cinput = do_filter_active(name);
        if (_cinput != name) {
            value = "";
        }
        $('input[name="' + name + '"]').val(value);

        <?php if($group == 'list'){ ?>
            $(table).DataTable().ajax.reload();
        <?php }elseif($group == 'chart'){ ?>
            init_stage_chart();
        <?php }else{ ?>
            stage_kanban();
        <?php } ?>
    }

    function init_stage_manage(){
    "use strict";
        <?php if($group == 'list'){ ?>
            init_stage_table();
        <?php }elseif($group == 'chart'){ ?>
            init_stage_chart();
        <?php }else{ ?>
            stage_kanban();
        <?php } ?>
    }

    function init_stage_table() {
      "use strict";

      if ($.fn.DataTable.isDataTable('.table-stages')) {
        $('.table-stages').DataTable().destroy();
      }
      initDataTable('.table-stages', admin_url + 'ma/stage_table', false, false, fnServerParams);
    }

    function init_stage_chart() {
    "use strict";

        $.each($('._hidden_inputs._filters input'),function(){
            fnServerParams[$(this).attr('name')] = $(this).val();
        });

        fnServerParams[$('input[name=csrf_token_name]').val()] = $('input[name=csrf_token_hash]').val();

        $.post(admin_url + 'ma/get_data_stage_chart', fnServerParams).done(function(res) {
        res = JSON.parse(res);
        
          Highcharts.chart('container_pie', {
            chart: {
              type: 'pie',
              options3d: {
                enabled: true,
                alpha: 45
              }
            },
            title: {
              text: '<?php echo _l('pie_statistics'); ?>'
            },
            plotOptions: {
              pie: {
                innerSize: 100,
                depth: 45
              }
            },
            credits: {
                enabled: false
            },
            series: [{
                innerSize: '20%',
                name: '<?php echo _l('stage'); ?>',
                data: res.data_stage_pie
              }]
          });

          Highcharts.chart('container_column', {
            chart: {
                type: 'column'
            },
            title: {
                text: '<?php echo _l('column_statistics'); ?>'
            },
            xAxis: {
                categories: res.data_stage_column.header
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: '<?php echo _l('total'); ?>'
                }
            },
            legend: {
        enabled: false
    },
            tooltip: {
                headerFormat: '<span class="font-size-10">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};" class="no-padding">{series.name}: </td>' +
                    '<td class="no-padding"><b>{point.y} </b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            series: [{
                name: "stage",
                colorByPoint: true,
                data: res.data_stage_column.data}
            ]
          });
        });
}


function stage_kanban_update(ui, object) {
  "use strict";
  if (object === ui.item.parent()[0]) {
      var data = {};
      data.category = $(ui.item.parent()[0]).parents('.stage-column').data('col-category-id');
      data.stage_id = $(ui.item).data('stage-id');

      check_stage_kanban_empty_col('[data-stage-id]');

      setTimeout(function() {
          $.post(admin_url + 'ma/update_stage_category', data)
      }, 50);
  }
}

function check_stage_kanban_empty_col(selector) {
    "use strict";
    var statuses = $('[data-col-category-id]');
    $.each(statuses, function (i, obj) {
        var total = $(obj).find(selector).length;
        if (total == 0) {
            $(obj).find('.kanban-empty').removeClass('hide');
            $(obj).find('.kanban-load-more').addClass('hide');
        } else {
            $(obj).find('.kanban-empty').addClass('hide');
        }
    });
}

function stage_kanban() {
  "use strict";
  init_stage_kanban('ma/stage_kanban', stage_kanban_update, '.stage-kanban', 445, 360, after_stage_kanban);
}

function after_stage_kanban() {
  "use strict";
  for (var i = -10; i < $('.task-phase').not('.color-not-auto-adjusted').length / 2; i++) {
      var r = 120;
      var g = 169;
      var b = 56;
      $('.task-phase:eq(' + (i + 10) + ')').not('.color-not-auto-adjusted').css('background', color(r - (i * 13), g - (i * 13), b - (i * 13))).css('border', '1px solid ' + color(r - (i * 12), g - (i * 12), b - (i * 12)));
  };
}

// General function to init kan ban based on settings
function init_stage_kanban(url, callbackUpdate, connect_with, column_px, container_px, callback_after_load) {
  "use strict";
    if ($('#load-kanban').length === 0) { return; }

    var scrollLeft = 0;
    $("#kanban-filters").appFilters({
        source: '<?php echo_uri("ma/stage_kanban"); ?>',
        targetSelector: '#load-kanban',
        reloadSelector: "#reload-kanban-button",
        filterDropdown: [
              
            ],
        rangeDatepicker: [{startDate: {name: "from_date", value: "<?php echo date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d')))); ?>"}, endDate: {name: "to_date", value: "<?php echo date('Y-m-d'); ?>"}, showClearButton: true}],
        beforeRelaodCallback: function () {
            scrollLeft = $("#kanban-wrapper").scrollLeft();
        },
        afterRelaodCallback: function () {
            setTimeout(function () {
                $("#kanban-wrapper").animate({scrollLeft: scrollLeft}, 'slow');
            }, 500);
        }
    });
}

// Switch field make request
function switch_field(field) {
    "use strict";

    var status, url, id;
    status = '0';
    if ($(field).prop('checked') === true) {
        status = '1';
    }
    url = $(field).data('switch-url');
    id = $(field).data('id');
    requestGet(url + '/' + id + '/' + status);
}
</script>