<script type="text/javascript">
var fnServerParams = {};
var site_url = $('input[name="site_url"]').val();
var admin_url  = $('input[name="site_url"]').val();
(function(){
  "use strict";
  $(".select2").select2();

    fnServerParams = {
        "email_template_id": '[name="email_template_id"]',
    }

   $.get(admin_url + 'ma/get_data_email_template_chart/'+$('input[name=email_template_id]').val()).done(function(res) {
    res = JSON.parse(res);

    Highcharts.chart('container', {
      chart: {
          type: 'area'
      },
      title: {
          text: 'Email Stats'
      },
    
      xAxis: {
          type: 'datetime',
          labels: {
              format: '{value:%Y-%m-%d}',
              rotation: 45,
              align: 'left'
          }
      },
      yAxis: {
          title: {
              text: ''
          }
      },
      credits: {
          enabled: false
      },
      series: res.data_email_template
    });

    Highcharts.chart('container_campaign', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Email Stats by Campaign'
    },
    xAxis: {
        categories: res.data_email_template_by_campaign.header,
        crosshair: true
    },
    yAxis: {
        title: {
            useHTML: true,
            text: ''
        }
    },
    credits: {
          enabled: false
      },
    tooltip: {
        headerFormat: '<span class="font-size-10">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};" class="no-padding">{series.name}: </td>' +
            '<td class="no-padding"><b>{point.y}</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: res.data_email_template_by_campaign.data
});
  });

   $('.add_language').on('click', function(){
      $('#language-modal').modal('show');
    });

    $('.clone_language').on('click', function(){
      $('#clone-design-modal').modal('show');
    });

  })(jQuery);

</script>
