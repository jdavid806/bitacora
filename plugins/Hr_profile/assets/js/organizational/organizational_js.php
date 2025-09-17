<script>  
	$(function(){
		'use strict';

		// $(".select2").select2();


		var tree_dep = $('#dep_tree').comboTree({
			source : <?php echo html_entity_decode($dep_tree);?>
		});

		var LeadsServerParams = {                
			"dept": "input[name='dept']",
		};

		var table_departments = $('.table-departments');

		initDataTable(table_departments, "<?php echo get_uri("hr_profile/get_data_department") ?>", [4], [4], LeadsServerParams, [2, 'asc']);

		$('#dep_tree').on('change', function() {
			$('#dept').val(tree_dep.getSelectedItemsId());
			table_departments.DataTable().ajax.reload();
		});
		

		$('#department').on('hidden.bs.modal', function(event) {
			$('#additional').html('');
			$('#department input[type="text"]').val('');
			$('#department input[type="email"]').val('');
			$('input[name="delete_after_import"]').prop('checked',false);
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
		});
	});


	function manage_departments(form) {

		'use strict';

		var data = $(form).serialize();
		var url = form.action;
		$.post(url, data).done(function(response) {
			response = JSON.parse(response);
			if(response.success == true){
				alert_float('success',response.message);
			}
			if(response.email_exist_as_staff == true) {
				window.location.reload();
			}
			$('.table-departments').DataTable().ajax.reload();
			$('#department').modal('hide');
		}).fail(function(data){
			var error = JSON.parse(data.responseText);
			alert_float('danger',error.message);
		});
		return false;
	}


	function new_department(){
		'use strict';

		$('#department').modal('show');
		$('#department').find('.edit-title').addClass('hide');
		$('#department').find('.add-title').removeClass('hide');
		$('#department select[name="manager_id"]').val('').change();
		$('#department select[name="parent_id"]').val('').change();
	}


	function edit_department(invoker,id){
		'use strict';

		$('#department').modal('show');
		$('#department').find('.edit-title').removeClass('hide');
		$('#department').find('.add-title').addClass('hide');
		
		$('#additional').append(hidden_input('id',id));
		$('#department input[name="title"]').val($(invoker).data('title'));

		var manager_id = $(invoker).data('manager_id');
		if(manager_id != 0 && manager_id != ''){
			$('#department select[name="manager_id"]').val(manager_id);
			$('#department select[name="manager_id"]').change();
		}else{
			$('#department select[name="manager_id"]').val('').change();
		}

		var parent_id = $(invoker).data('parent_id');
		if(parent_id != 0 && parent_id != ''){
			$('#department select[name="parent_id"]').val(parent_id);
			$('#department select[name="parent_id"]').change();

		}else{
			$('#department select[name="parent_id"]').val('').change();
		}

		$('.add-title').addClass('hide');
	}


	function test_dep_imap_connection(){
		'use strict';

		var data = {};
		data.email = $('input[name="email"]').val();
		data.password = $('input[name="password"]').val();
		data.host = $('input[name="host"]').val();
		data.username = $('input[name="imap_username"]').val();
		data.encryption = $('input[name="encryption"]:checked').val();

		$.post("<?php echo get_uri("hr_profile/test_imap_connection") ?>",data).done(function(response){
			response = JSON.parse(response);
			alert_float(response.alert_type,response.message);
		});
	}



	function zen_unit_chart (department) {
		'use strict';

		$.post("<?php echo get_uri("hr_profile/zen_unit_chart/") ?>"+department).done(function(response){
			response = JSON.parse(response);

			ds = {
				'image':'' ,
				'name': '<p class="bold" class="zen_unit_chart"><i class="fa fa-sitemap"></i>'+' '+response.dpm_name+' </p>',
				'staff_identifi': '',
				'job_position_url': 'Javascript:void(0);',
				'job_position': '',
				'staff_email': response.html,
				'phonenumber':'',
				'children': response.data
			};

			$('#department_chart').init({ 'data':ds });
		});
	};



	window.onload = function () {
		'use strict';
						//custom node template
						
						var nodeTemplate = function(data) {
							if(data.name != ''){
								return `
								<a href="#" data-toggle="sidebar-right" data-target=".unit-chart-modal" ><div class="bg-fab017 p-5 organizational">${data.name}
								</div>
								<div class ="content chart_company_name">${data.image}${data.title}</div>

								<div class="content chart_company_name"><span data-feather="codepen" class="icon-16"></span>  ${data.job_position}</div>

								<div class ="content chart_company_name"><span data-feather="users" class="icon-16"></span>  ${data.reality_now}</div>
								</a>
								`;
							}else{
								return `
								<a href="#" data-toggle="sidebar-right" data-target=".unit-chart-modal" >
								<div class="content">${data.image}${data.title}</div>
								<div class="content chart_company_name"></div>
								</a>
								`;
							}
							
						};

						var img_dir = '<?php echo get_file_from_setting("site_logo", true); ?>';

						var ds = {
							'name': '',
							'image':'<img class="img_logo"  src=" '+img_dir+' ">' ,
							'title':'<p class="title_company"><?php echo get_setting('app_title'); ?></p>',
							'reality_now':'',
							'children': <?php echo html_entity_decode($deparment_chart); ?>
						};
						var oc = $('#department_chart').orgchart({
							'data' :ds ,
							'nodeTemplate': nodeTemplate,
							'pan': true,
							'zoom': true,
							verticalLevel: 100,
							visibleLevel: 100,
							'toggleSiblingsResp': true,
							'createNode': function($node, data) {
								$node.on('click', function(event) {
									if (!$(event.target).is('.edge, .toggleBtn')) {
										var $this = $(this);
										var $chart = $this.closest('.orgchart');
										var newX = window.parseInt(($chart.outerWidth(true)/2) - ($this.offset().left - $chart.offset().left) - ($this.outerWidth(true)/2));
										var newY = window.parseInt(($chart.outerHeight(true)/2) - ($this.offset().top - $chart.offset().top) - ($this.outerHeight(true)/2));
										$chart.css('transform', 'matrix(1, 0, 0, 1, ' + newX + ', ' + newY + ')');
									}
								});
							}

						});
					};

			function view_department_chart(){
				'use strict';
				$('#department_chart_view').modal('show');
			}


</script>