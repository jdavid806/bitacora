
// Set datatables error throw console log
$.fn.dataTable.ext.errMode = 'throw';
$.fn.dataTableExt.oStdClasses.sWrapper = 'dataTables_wrapper form-inline dt-bootstrap table-loading pt15 pl15 pr15';

// Predefined global variables
var original_top_search_val,
table_leads,
table_activity_log,
table_estimates,
table_invoices,
table_tasks,
side_bar = $('#sidebar-menu'),
content_wrapper = $('#wrapper'),
setup_menu = $('#setup-menu-wrapper'),
menu_href_selector,
calendar_selector = $('#calendar'),
notifications_wrapper = $('#header').find('li.notifications-wrapper'),
doc_initial_title = document.title,
newsfeed_posts_page = 0,
track_load_post_likes = 0,
track_load_comment_likes = 0,
post_likes_total_pages = 0,
comment_likes_total_pages = 0,
select_picker_validated_event = false,
postid = 0,
lastAddedItemKey = null,
setup_menu_item = $('#setup-menu-item');

(function(){
  "use strict";
	$( document ).ready(function() {
		$('a[href*="/ma/dashboard"]').parents('li.expand.main').addClass('open');
	});
})(jQuery);

// General function for all datatables serverside
function initDataTable(selector, url, notsearchable, notsortable, fnserverparams, defaultorder) {
	var table = typeof (selector) == 'string' ? $("body").find('table' + selector) : selector;

	if (table.length === 0) {
		return false;
	}

	fnserverparams = (fnserverparams == 'undefined' || typeof (fnserverparams) == 'undefined') ? [] : fnserverparams;

	// If not order is passed order by the first column
	if (typeof (defaultorder) == 'undefined') {
		defaultorder = [
		[0, 'asc']
		];
	} else {
		if (defaultorder.length === 1) {
			defaultorder = [defaultorder];
		}
	}

	var user_table_default_order = table.attr('data-default-order');

	if (!empty(user_table_default_order)) {
		var tmp_new_default_order = JSON.parse(user_table_default_order);
		var new_defaultorder = [];
		for (var i in tmp_new_default_order) {
			// If the order index do not exists will throw errors
			if (table.find('thead th:eq(' + tmp_new_default_order[i][0] + ')').length > 0) {
				new_defaultorder.push(tmp_new_default_order[i]);
			}
		}
		if (new_defaultorder.length > 0) {
			defaultorder = new_defaultorder;
		}
	}

	var length_options = [10, 25, 50, 100];
	var length_options_names = [10, 25, 50, 100];

	//set default display length
	var displayLength = AppHelper.settings.displayLength * 1;

	if (isNaN(displayLength) || !displayLength) {
		displayLength = 10;
	}

	if ($.inArray(displayLength, length_options) == -1) {
		length_options.push(displayLength);
		length_options_names.push(displayLength);
	}

	length_options.sort(function (a, b) {
		return a - b;
	});
	length_options_names.sort(function (a, b) {
		return a - b;
	});

	length_options.push(-1);
	length_options_names.push(AppLanugage.all);

	var dtSettings = {
		// "language": app.lang.datatables,
		"processing": true,
		"retrieve": true,
		"serverSide": true,
		'paginate': true,
		'searchDelay': 750,
		"bDeferRender": true,
		"autoWidth": false,
		dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'col-md-8'p>><'row'<'#colvis'><'.dt-page-jump'>>",
		"pageLength": displayLength,
		"lengthMenu": [length_options, length_options_names],
		"columnDefs": [{
			"searchable": false,
			"targets": notsearchable,
		}, {
			"sortable": false,
			"targets": notsortable
		}],
		"fnDrawCallback": function (oSettings) {
			_table_jump_to_page(this, oSettings);
			if (oSettings.aoData.length === 0) {
				$(oSettings.nTableWrapper).addClass('app_dt_empty');
			} else {
				$(oSettings.nTableWrapper).removeClass('app_dt_empty');
			}
		},
		"fnCreatedRow": function (nRow, aData, iDataIndex) {
			// If tooltips found
			$(nRow).attr('data-title', aData.Data_Title);
			$(nRow).attr('data-toggle', aData.Data_Toggle);
		},
		"initComplete": function (settings, json) {
			var t = this;
			var $btnReload = $('.btn-dt-reload');
			$btnReload.attr('data-toggle', 'tooltip');
			$btnReload.attr('title', 'Reload');

			var $btnColVis = $('.dt-column-visibility');
			$btnColVis.attr('data-toggle', 'tooltip');
			$btnColVis.attr('title', 'Visibility');

			t.wrap('<div class="table-responsive"></div>');

			var dtEmpty = t.find('.dataTables_empty');
			if (dtEmpty.length) {
				dtEmpty.attr('colspan', t.find('thead th').length);
			}

			// Hide mass selection because causing issue on small devices
			if (is_mobile() && $(window).width() < 400 && t.find('tbody td:first-child input[type="checkbox"]').length > 0) {
				t.DataTable().column(0).visible(false, false).columns.adjust();
				$("a[data-target*='bulk_actions']").addClass('hide');
			}

			t.parents('.table-loading').removeClass('table-loading');
			t.removeClass('dt-table-loading');
			var th_last_child = t.find('thead th:last-child');
			var th_first_child = t.find('thead th:first-child');
			
			if (th_first_child.find('input[type="checkbox"]').length > 0) {
				th_first_child.addClass('not-export');
			}
			mainWrapperHeightFix();
		},
		"order": defaultorder,
		"ajax": {
			"url": url,
			"type": "POST",
			"data": function (d) {
				if (typeof (csrfData) !== 'undefined') {
					d[csrfData['token_name']] = csrfData['hash'];
				}
				for (var key in fnserverparams) {
					d[key] = $(fnserverparams[key]).val();
				}
				if (table.attr('data-last-order-identifier')) {
					d['last_order_identifier'] = table.attr('data-last-order-identifier');
				}
			}
		},
		"language": {
			lengthMenu: "_MENU_",
			zeroRecords: AppLanugage.noRecordFound,
			info: "_START_-_END_ / _TOTAL_",
			sInfo: "_START_-_END_ / _TOTAL_",
			infoFiltered: "(_MAX_)",
			search: "",
			searchPlaceholder: AppLanugage.search,
			sInfoEmpty: "0-0 / 0",
			sInfoFiltered: "(_MAX_)",
			sInfoPostFix: "",
			sInfoThousands: ",",
			sProcessing: "<div class='table-loader'><span class='loading'></span></div>",
			"oPaginate": {
				"sPrevious": "<i data-feather='chevrons-left' class='icon-16'></i>",
				"sNext": "<i data-feather='chevrons-right' class='icon-16'></i>"
			}

		},
		buttons: get_datatable_buttons(table),
	};

	table = table.dataTable(dtSettings);
	var tableApi = table.DataTable();

	var hiddenHeadings = table.find('th.not_visible');
	var hiddenIndexes = [];

	$.each(hiddenHeadings, function () {
		hiddenIndexes.push(this.cellIndex);
	});

	setTimeout(function () {
		for (var i in hiddenIndexes) {
			tableApi.columns(hiddenIndexes[i]).visible(false, false).columns.adjust();
		}
	}, 10);

	if (table.hasClass('customizable-table')) {
		var tableToggleAbleHeadings = table.find('th.toggleable');
		var invisible = $('#hidden-columns-' + table.attr('id'));
		try {
			invisible = JSON.parse(invisible.text());
		} catch (err) {
			invisible = [];
		}

		$.each(tableToggleAbleHeadings, function () {
			var cID = $(this).attr('id');
			if ($.inArray(cID, invisible) > -1) {
				tableApi.column('#' + cID).visible(false);
			}
		});

		
	}

	// Fix for hidden tables colspan not correct if the table is empty
	if (table.is(':hidden')) {
		table.find('.dataTables_empty').attr('colspan', table.find('thead th').length);
	}

	table.on('preXhr.dt', function (e, settings, data) {
		if (settings.jqXHR) settings.jqXHR.abort();
	});

	return tableApi;
}

// Check if field is empty
function empty(data) {
	if (typeof(data) == 'number' || typeof(data) == 'boolean') {
		return false;
	}
	if (typeof(data) == 'undefined' || data === null) {
		return true;
	}
	if (typeof(data.length) != 'undefined') {
		return data.length === 0;
	}
	var count = 0;
	for (var i in data) {
		if (data.hasOwnProperty(i)) {
			count++;
		}
	}
	return count === 0;
}


// Returns datatbles export button array based on settings
// Admin area only
function get_datatable_buttons(table) {
	
	var formatExport = {
		body: function(data, row, column, node) {

			// Fix for notes inline datatables
			// Causing issues because of the hidden textarea for edit and the content is duplicating
			// This logic may be extended in future for other similar fixes
			var newTmpRow = $('<div></div>', data);
			newTmpRow.append(data);

			if (newTmpRow.find('[data-note-edit-textarea]').length > 0) {
				newTmpRow.find('[data-note-edit-textarea]').remove();
				data = newTmpRow.html().trim();
			}
			// Convert e.q. two months ago to actual date
			var exportTextHasActionDate = newTmpRow.find('.text-has-action.is-date');

			if(exportTextHasActionDate.length) {
				data = exportTextHasActionDate.attr('data-title');
			}

			if (newTmpRow.find('.row-options').length > 0) {
				newTmpRow.find('.row-options').remove();
				data = newTmpRow.html().trim();
			}

			if (newTmpRow.find('.table-export-exclude').length > 0) {
				newTmpRow.find('.table-export-exclude').remove();
				data = newTmpRow.html().trim();
			}


			// Datatables use the same implementation to strip the html.
			var div = document.createElement("div");
			div.innerHTML = data;
			var text = div.textContent || div.innerText || "";

			return text.trim();
		}
	};
	var table_buttons_options = [];

	if (typeof(table_export_button_is_hidden) != 'function' || !table_export_button_is_hidden()) {
		table_buttons_options.push({
			extend: 'collection',
			text: 'Export',
			className: 'btn btn-default-dt-options',
			buttons: [{
				extend: 'excel',
				text:AppLanugage.excel,
				footer: true,
				exportOptions: {
					columns: [':not(.not-export)'],
					rows: function(index) {
						return _dt_maybe_export_only_selected_rows(index, table);
					},
					format: formatExport,
				},
			}, {
				
				extend: 'pdfHtml5',
				text: AppLanugage.print,
				footer: true,
				exportOptions: {
					columns: [':not(.not-export)'],
					rows: function(index) {
						return _dt_maybe_export_only_selected_rows(index, table);
					},
					format: formatExport,
				},
				orientation: 'landscape',
				customize: function(doc) {
					// Fix for column widths
					var table_api = $(table).DataTable();
					var columns = table_api.columns().visible();
					var columns_total = columns.length;
					var total_visible_columns = 0;

					for (i = 0; i < columns_total; i++) {
						// Is only visible column
						if (columns[i] == true) {
							total_visible_columns++;
						}
					}

					setTimeout(function() {
						if (total_visible_columns <= 5) {
							var pdf_widths = [];
							for (i = 0; i < total_visible_columns; i++) {
								pdf_widths.push((735 / total_visible_columns));
							}

							doc.content[1].table.widths = pdf_widths;
						}
					}, 10);

					if (app.user_language.toLowerCase() == 'persian' || app.user_language.toLowerCase() == 'arabic') {
						doc.defaultStyle.font = Object.keys(pdfMake.fonts)[0];
					}

					doc.styles.tableHeader.alignment = 'left';
					doc.defaultStyle.fontSize = 10;

					doc.styles.tableHeader.fontSize = 10;
					doc.styles.tableHeader.margin = [3, 3, 3, 3];

					doc.styles.tableFooter.fontSize = 10;
					doc.styles.tableFooter.margin = [3, 0, 0, 0];

					doc.pageMargins = [2, 20, 2, 20];
				}
			}, {
				extend: 'print',
				text: AppLanugage.print,
				footer: true,
				exportOptions: {
					columns: [':not(.not-export)'],
					rows: function(index) {
						return _dt_maybe_export_only_selected_rows(index, table);
					},
					format: formatExport,
				}
			}],
		});
	}
	var tableButtons = $("body").find('.table-btn');

	$.each(tableButtons, function() {
		var b = $(this);
		if (b.length && b.attr('data-table')) {
			if ($(table).is(b.attr('data-table'))) {
				table_buttons_options.push({
					text: b.text().trim(),
					className: 'btn btn-default-dt-options',
					action: function(e, dt, node, config) {
						b.click();
					}
				});
			}
		}
	});

	if (!$(table).hasClass('dt-inline')) {
		table_buttons_options.push({
			text: '<span data-feather="refresh-cw" class="icon-16"></span>',
			className: 'btn btn-default-dt-options btn-dt-reload',
			action: function(e, dt, node, config) {
				dt.ajax.reload();
			}
		});
	}
	

	return table_buttons_options;
}

// Datatables custom job to page function
function _table_jump_to_page(table, oSettings) {

	var paginationData = table.DataTable().page.info();
	var previousDtPageJump = $("body").find('#dt-page-jump-' + oSettings.sTableId);

	if (previousDtPageJump.length) {
		previousDtPageJump.remove();
	}

	if (paginationData.pages > 1) {

		var jumpToPageSelect = $("<select></select>", {
			"data-id": oSettings.sTableId,
			"class": "dt-page-jump-select form-control",
			'id': 'dt-page-jump-' + oSettings.sTableId
		});

		var paginationHtml = '';

		for (var i = 1; i <= paginationData.pages; i++) {
			var selectedCurrentPage = ((paginationData.page + 1) === i) ? 'selected' : '';
			paginationHtml += "<option value='" + i + "'" + selectedCurrentPage + ">" + i + "</option>";
		}

		if (paginationHtml != '') {
			jumpToPageSelect.append(paginationHtml);
		}

	}
}

function is_mobile() {
	if($(window).width() < 800){
		return true;
	}
	return false;

}

// Fix for height on the wrapper
function mainWrapperHeightFix() {
	// Get and set current height
	var headerH = 63;
	var navigationH = side_bar.height();
	var contentH = $("#wrapper").find('.content').height();
	setup_menu.css('min-height', ($(document).outerHeight(true) - (headerH * 2)) + 'px');

	content_wrapper.css('min-height', $(document).outerHeight(true) - headerH + 'px');
	// Set new height when content height is less then navigation
	if (contentH < navigationH) {
		content_wrapper.css("min-height", navigationH + 'px');
	}

	// Set new height when content height is less then navigation and navigation is less then window
	if (contentH < navigationH && navigationH < $(window).height()) {
		content_wrapper.css("min-height", $(window).height() - headerH + 'px');
	}
	// Set new height when content is higher then navigation but less then window
	if (contentH > navigationH && contentH < $(window).height()) {
		content_wrapper.css("min-height", $(window).height() - headerH + 'px');
	}
	// Fix for RTL main admin menu height
	var isRTL = false;
	if (is_mobile() && isRTL == 'true') {
		side_bar.css('min-height', $(document).outerHeight(true) - headerH + 'px');
	}
}


	// On mass_select all select all the availble rows in the tables.
	$("body").on('change', '#mass_select_all', function () {
		var to, rows, checked;
		to = $(this).data('to-table');

		rows = $('.table-' + to).find('tbody tr');
		checked = $(this).prop('checked');
		$.each(rows, function () {
			$($(this).find('td').eq(0)).find('input').prop('checked', checked);
		});
	});

	function _dt_maybe_export_only_selected_rows(index, table) {
		table = $(table);
		index = index.toString();
		var bulkActionsCheckbox = table.find('thead th input[type="checkbox"]').eq(0);
		if (bulkActionsCheckbox && bulkActionsCheckbox.length > 0) {
			var rows = table.find('tbody tr');
			var anyChecked = false;
			$.each(rows, function() {
				if ($(this).find('td:first input[type="checkbox"]:checked').length) {
					anyChecked = true;
				}
			});

			if (anyChecked) {
				if (table.find('tbody tr:eq(' + (index) + ') td:first input[type="checkbox"]:checked').length > 0) {
					return index;
				} else {
					return null;
				}
			} else {
				return index;
			}
		}
		return index;
	}

	function init_ajax_search(type, selector, server_data, url) {
		var ajaxSelector = $('body').find(selector);

		if (ajaxSelector.length) {
			var options = {
				ajax: {
					url: (typeof (url) == 'undefined' ? admin_url + 'misc/get_relation_data' : url),
					data: function () {
						var data = {};
						data.type = type;
						data.rel_id = '';
						data.q = '{{{q}}}';
						if (typeof (server_data) != 'undefined') {
							jQuery.extend(data, server_data);
						}
						return data;
					}
				},
				locale: {
					emptyTitle: app.lang.search_ajax_empty,
					statusInitialized: app.lang.search_ajax_initialized,
					statusSearching: app.lang.search_ajax_searching,
					statusNoResults: app.lang.not_results_found,
					searchPlaceholder: app.lang.search_ajax_placeholder,
					currentlySelected: app.lang.currently_selected
				},
				requestDelay: 500,
				cache: false,
				preprocessData: function (processData) {
					var bs_data = [];
					var len = processData.length;
					for (var i = 0; i < len; i++) {
						var tmp_data = {
							'value': processData[i].id,
							'text': processData[i].name,
						};
						if (processData[i].subtext) {
							tmp_data.data = {
								subtext: processData[i].subtext
							};
						}
						bs_data.push(tmp_data);
					}
					return bs_data;
				},
				preserveSelectedPosition: 'after',
				preserveSelected: true
			};
			if (ajaxSelector.data('empty-title')) {
				options.locale.emptyTitle = ajaxSelector.data('empty-title');
			}
			ajaxSelector.select2({data: options});
		}
	}

	// Generate hidden input field
	function hidden_input(name, val) {
		return '<input type="hidden" name="' + name + '" value="' + val + '">';
	}

	// General helper function for $.get ajax requests
	function requestGet(uri, params) {
		params = typeof (params) == 'undefined' ? {} : params;
		var options = {
			type: 'GET',
			url: uri
		};
		return $.ajax($.extend({}, options, params));
	}

	// General helper function for $.get ajax requests with dataType JSON
	function requestGetJSON(uri, params) {
		params = typeof (params) == 'undefined' ? {} : params;
		params.dataType = 'json';
		return requestGet(uri, params);
	}

	// Function to slug string
	function slugify(string) {
		return string
		.toString()
		.trim()
		.toLowerCase()
		.replace(/\s+/g, "-")
		.replace(/[^\w\-]+/g, "")
		.replace(/\-\-+/g, "-")
		.replace(/^-+/, "")
		.replace(/-+$/, "");
	}

	// Check if field is empty
	function empty(data) {
		if (typeof(data) == 'number' || typeof(data) == 'boolean') {
			return false;
		}
		if (typeof(data) == 'undefined' || data === null) {
			return true;
		}
		if (typeof(data.length) != 'undefined') {
			return data.length === 0;
		}
		var count = 0;
		for (var i in data) {
			if (data.hasOwnProperty(i)) {
				count++;
			}
		}
		return count === 0;
	}

	function delivery_status_mark_as(status, task_id, type) {

		url = 'delivery_status_mark_as/' + status + '/' + task_id + '/' + type;
		var taskModalVisible = $('#task-modal').is(':visible');
		url += '?single_task=' + taskModalVisible;
		$("body").append('<div class="dt-loader"></div>');

		requestGetJSON(url).done(function (response) {
			$("body").find('.dt-loader').remove();
			if (response.success === true || response.success == 'true') {

				var av_tasks_tables = ['.table-table_manage_delivery', '.table-table_manage_packing_list'];
				$.each(av_tasks_tables, function (i, selector) {
					if ($.fn.DataTable.isDataTable(selector)) {
						$(selector).DataTable().ajax.reload(null, false);
					}
				});
				appAlert.success(response.message);
			}
		});
	}


	function formatNumber(n) {
		'use strict';

		return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
	}

	function formatCurrency(input, blur) {
		"use strict";

		var input_val = input.val();
		if (input_val === "") { return; }
		var original_len = input_val.length; 
		var caret_pos = input.prop("selectionStart");
		if (input_val.indexOf(".") >= 0) {
			var decimal_pos = input_val.indexOf(".");
			var left_side = input_val.substring(0, decimal_pos);
			var right_side = input_val.substring(decimal_pos);
			left_side = formatNumber(left_side);
			right_side = formatNumber(right_side);
			right_side = right_side.substring(0, 2);
			input_val = left_side + "." + right_side;
		} else {

			input_val = formatNumber(input_val);
			input_val = input_val;
		}
		input.val(input_val);
		var updated_len = input_val.length;
		caret_pos = updated_len - original_len + caret_pos;
		input[0].setSelectionRange(caret_pos, caret_pos);
	}
	/*Init tinymce editors*/
	init_editor();


	/*Function to init the tinymce editor*/
	function init_editor(selector, settings) {

		selector = typeof (selector) == 'undefined' ? '.tinymce' : selector;
		var _editor_selector_check = $(selector);

		if (_editor_selector_check.length === 0) {
			return;
		}

		$.each(_editor_selector_check, function () {
			if ($(this).hasClass('tinymce-manual')) {
				$(this).removeClass('tinymce');
			}
		});

		/*    Original settings*/
		var _settings = {
			branding: false,
			selector: selector,
			browser_spellcheck: true,
			height: 400,
			theme: 'modern',
			skin: 'perfex',
    	// language: app.tinymce_lang,
    	relative_urls: false,
    	inline_styles: true,
    	verify_html: false,
    	cleanup: false,
    	autoresize_bottom_margin: 25,
    	valid_elements: '+*[*]',
    	valid_children: "+body[style], +style[type]",
    	apply_source_formatting: false,
    	remove_script_host: false,
    	removed_menuitems: 'newdocument restoredraft',
    	forced_root_block: false,
    	autosave_restore_when_empty: false,
    	fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
    	setup: function (ed) {
            // Default fontsize is 12
            ed.on('init', function () {
            	this.getDoc().body.style.fontSize = '12pt';
            });
        },
        table_default_styles: {
            // Default all tables width 100%
            width: '100%',
        },
        plugins: [
        'advlist autoresize autosave lists link image print hr codesample',
        'visualblocks code fullscreen',
        'media save table contextmenu',
        'paste textcolor colorpicker'
        ],
        toolbar1: 'fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft',
        // file_browser_callback: elFinderBrowser,
        contextmenu: "link image inserttable | cell row column deletetable | paste",
    };

    // Add the rtl to the settings if is true
    var isRTL = true;
    isRTL == 'true' ? _settings.directionality = 'rtl' : '';
    isRTL == 'true' ? _settings.plugins[0] += ' directionality' : '';

    // Possible settings passed to be overwrited or added
    if (typeof (settings) != 'undefined') {
    	for (var key in settings) {
    		if (key != 'append_plugins') {
    			_settings[key] = settings[key];
    		} else {
    			_settings['plugins'].push(settings[key]);
    		}
    	}
    }

    // Init the editor
    var editor = tinymce.init(_settings);
    $(document).trigger('app.editor.initialized');

    return editor;
}

function initColorPicker(){
	$(".color-palet span").on('click', function () {
        $(".color-palet").find(".active").removeClass("active");
        $(this).addClass("active");
        $("#custom-color").val($(this).attr("data-color"));
    });

    $(".color-palet #custom-color").on('click', function () {
        $(".color-palet").find(".active").removeClass("active");
        $(this).addClass("active");
    });
}

$("body").on('change', '.onoffswitch input', function (event, state) {
	"use strict";

	var $selector = $(this),
	switch_url = $selector.attr('data-switch-url');
	if (!switch_url) {
		return;
	}

	switch_field(this);
});

// Switch field make request
function switch_field(field) {
	"use strict";

	var status, url, id;
	status = 'inactive';
	if ($(field).prop('checked') === true) {
		status = 'active';
	}
	url = $(field).data('switch-url');
	id = $(field).data('id');
	requestGet(url + '/' + id + '/' + status);
}

