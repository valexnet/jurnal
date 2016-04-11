
var top_menu;
var name_menu;
var first_page = 0;
function show(a)
	{
		top_menu = '';
		name_menu = '';
		$('#info_content, #form_content, #ajax_content, #informer_body').html('');
		
		$('#top_menu > a').removeClass('btn-primary');
		$('#top_menu > a').addClass('btn-default');
		$('#top_menu_action').addClass('hidden');
		$('#top_menu_action > a').addClass('hidden');

		selected_row = 0;
		selected_kt_row = 0;
		selected_pz_row = 0;
		selected_invent_nom = 0;
		selected_db_kt_id = 0;
		hot_keys = true;

		$('#button_glyphicon_tasks').removeClass('btn-primary');
		$('#button_glyphicon_tasks').addClass('btn-default');
		$('#button_glyphicon_cd').removeClass('btn-primary');
		$('#button_glyphicon_cd').addClass('btn-default');

		if (a == 'invent')
			{
				top_menu = 'invent';
				name_menu = 'Інвентарні номери';
				$('#top_menu_action').removeClass('hidden');
				$('#button_glyphicon_plus').removeClass('hidden');
				$('#button_glyphicon_refresh').removeClass('hidden');
				$('#button_glyphicon_print').removeClass('hidden');
				$('#top_menu_button_invent').removeClass('btn-default');
				$('#top_menu_button_invent').addClass('btn-primary');
				load();
			}

		if (a == 'kt')
			{
				top_menu = 'kt';
				$('#top_menu_action').removeClass('hidden');
				$('#button_glyphicon_plus').removeClass('hidden');
				$('#button_glyphicon_refresh').removeClass('hidden');
				$('#top_menu_button_kt').removeClass('btn-default');
				$('#top_menu_button_kt').addClass('btn-primary');
				$('#ajax_content').html('У розробці');
			}

		if (a == 'pz')
			{
				top_menu = 'pz';
				$('#top_menu_button_pz').removeClass('btn-default');
				$('#top_menu_button_pz').addClass('btn-primary');
				$('#ajax_content').html('У розробці');
			}

		if (a == 'request')
			{
				top_menu = 'request';
				$('#top_menu_button_request').removeClass('btn-default');
				$('#top_menu_button_request').addClass('btn-primary');
				$('#ajax_content').html('У розробці');
			}

		if (a == 'repair')
			{
				top_menu = 'repair';
				$('#top_menu_button_repair').removeClass('btn-default');
				$('#top_menu_button_repair').addClass('btn-primary');
				$('#ajax_content').html('У розробці');
			}

		if (a == 'rooms')
			{
				top_menu = 'rooms';
				name_menu = 'Перелік кімнат';
				$('#top_menu_action').removeClass('hidden');
				$('#button_glyphicon_plus').removeClass('hidden');
				$('#button_glyphicon_refresh').removeClass('hidden');
				$('#button_glyphicon_print').removeClass('hidden');
				$('#top_menu_button_rooms').removeClass('btn-default');
				$('#top_menu_button_rooms').addClass('btn-primary');
				load();
			}

		if (a == 'status')
			{
				top_menu = 'status';
				name_menu = 'Перелік статусів';
				$('#top_menu_action').removeClass('hidden');
				$('#button_glyphicon_plus').removeClass('hidden');
				$('#button_glyphicon_refresh').removeClass('hidden');
				$('#button_glyphicon_print').removeClass('hidden');
				$('#top_menu_button_status').removeClass('btn-default');
				$('#top_menu_button_status').addClass('btn-primary');
				load();
			}
	};

function load()
	{
		hot_keys = true;
		selected_db_kt_id = 0;
		$('#button_glyphicon_trash').addClass('hidden');
		$('.glyphicon glyphicon-trash').html('');

		$('#form_content').html('');
		$('#progress').show();
		$('#ajax_content').load('jurnal_it_ajax.php', {menu: top_menu, act: 'load', selected_kt_row: selected_kt_row, selected_pz_row: selected_pz_row},
			function()
				{
					$('#progress').hide();
				}
		);
	};

function loadHelp(a, b, c)
    {
		$(a).load('jurnal_it_ajax.php', {menu: top_menu, act: 'loadhelp', help_name: a, from: b, input: c, selected_kt_row: selected_kt_row, selected_pz_row: selected_pz_row});
    };

function insertData(a, b)
	{
		$(a).val(b);
		$('.help_div').html('');
	};

var rotate_print = 1;
function print()
	{
		hot_keys = true;
		if (rotate_print == 1)
			{
				rotate_print = 0;
				$('.container').hide();
				$('#info_content').html('<center><h3 onclick="print()">' + name_menu + '</h3></center>');
				$('#ajax_content').removeClass('container');
				$('#ajax_content').addClass('container_print');
				$('#info_content, #ajax_content').show();
			}
			else
			{
				rotate_print = 1;
				$('#info_content').html('');
				$('#ajax_content').removeClass('container_print');
				$('#ajax_content').addClass('container');
				$('.container').show();
				$('#progress').hide();
			}
	};

function plus(edit_id)
	{
		hot_keys = true;
		selected_db_kt_id = 0;
		$('#button_glyphicon_trash').addClass('hidden');
		$('.glyphicon glyphicon-trash').html('');

		$('#progress').show();
		$('#form_content').load('jurnal_it_ajax.php', {menu: top_menu, act: 'get_form_plus', selected_kt_row: selected_kt_row, selected_pz_row: selected_pz_row},
			function()
			{
				$('#progress').hide();
				show_plus_form(edit_id);
			}
		);
	};

var selected_row = 0;
var selected_kt_row = 0;
var selected_pz_row = 0;
var selected_invent_nom = 0;
function select_one_row(id)
	{
		hot_keys = true;
		$('#button_glyphicon_tasks').addClass('hidden');
		$('.glyphicon-tasks').html('');
		$('#button_glyphicon_cd').addClass('hidden');
		$('.glyphicon-cd').html('');
		$('#button_glyphicon_trash').addClass('hidden');
		$('.glyphicon-trash').html('');
		if (top_menu == 'invent')
			{
				selected_row = id;
				selected_invent_nom = $('#invent_' + id).html();
				//$('.glyphicon-tasks').html('');
				$('#button_glyphicon_tasks').removeClass('hidden');
				//$('.glyphicon-cd').html(' ' + $('#invent_' + id).html());
				$('#button_glyphicon_cd').removeClass('hidden');
				//$('.glyphicon-trash').html(' ' + $('#invent_' + id).html());
				$('#button_glyphicon_trash').removeClass('hidden');
				
				$('#tbody_content > tr').removeClass('success');
				$('#row_' + id).addClass('success');
				$('.kt_info').remove();
				$('#progress').show();
				$('#hide_content').load('jurnal_it_ajax.php', {menu: top_menu, act: 'load_row_info', selected_row: selected_row}, function() {insert_kt_row_info(id); $('#progress').hide();});
			}
	};

function insert_kt_row_info(id)
	{
		hot_keys = true;
		var row_info_kt = $('#hide_content').html();
		$(row_info_kt).insertAfter($('#row_' + id));
	}

var selected_db_kt_id = 0;
function select_db_kt_row(id)
	{
		hot_keys = true;
		selected_db_kt_id = id;
		selected_db_kt_name = $('#name_' + id).html();
		$('.glyphicon-trash').html('');
		$('#button_glyphicon_trash').removeClass('hidden');
		
		//$('#table_content .warning').addClass('hidden');
		$('#table_content > .default').addClass('hidden');
		$('#row_hidden_' + id).removeClass('hidden');
	}
	
function showKT()
	{
		selected_db_kt_id = 0;
		name_menu = 'Інвентарний номер:' + selected_invent_nom + '<br />Список техніки';

		selected_pz_row = 0;
		$('.glyphicon-cd').html('');
		$('#button_glyphicon_cd').removeClass('btn-primary');
		$('#button_glyphicon_cd').addClass('btn-default');
		$('.glyphicon-trash').html('');
		$('#button_glyphicon_trash').addClass('hidden');

		selected_kt_row = selected_row;
		selected_kt_name = $('#name_' + selected_kt_row).html();
		$('#button_glyphicon_tasks').removeClass('btn-default');
		$('#button_glyphicon_tasks').addClass('btn-primary');
		$('.glyphicon-tasks').html('<br>Т<br>Е<br>Х<br>Н<br>І<br>К<br>А<br>');
		load();
	}

function showPZ()
	{
		selected_db_kt_id = 0;
		name_menu = 'Інвентарний номер:' + selected_invent_nom + '<br />Список програмного забезпечення';

		selected_kt_row = 0;
		$('.glyphicon-tasks').html('');
		$('#button_glyphicon_tasks').removeClass('btn-primary');
		$('#button_glyphicon_tasks').addClass('btn-default');
		$('.glyphicon-trash').html('');
		$('#button_glyphicon_trash').addClass('hidden');

		selected_pz_row = selected_row;
		$('#button_glyphicon_cd').removeClass('btn-default');
		$('#button_glyphicon_cd').addClass('btn-primary');
		$('.glyphicon-cd').html('<br>П<br>Р<br>О<br>Г<br>Р<br>А<br>М<br>И<br>');
		load();
	}

function show_plus_form(edit_id)
	{
		hot_keys = false;
		if (top_menu == 'invent')
			{
				if (selected_kt_row == 0 && selected_pz_row == 0)
					{
						if (edit_id == '0')
							{
								$('#invent_modalLabel').html('Додати інвентарний номер');
								$('#add_button').html('Додати інвентарний номер');
								$('#delete_button').hide();
								$('#edit_id').val('0');
								$('#amort').val('0');
							}
							else
							{
								$('#invent_modalLabel').html('Редагувати інвентарний номер');
								$('#add_button').html('Редагувати інвентарний номер');
								$('#delete_button').show();
								$('#edit_id').val(edit_id);
								$('#invent').val($('#invent_' + edit_id).html());
								$('#inv_plus').val($('#inv_plus_' + edit_id).html());
								$('#name').val($('#name_' + edit_id).html());
								$('#data_made').val($('#data_made_' + edit_id).html());
								$('#data_install').val($('#data_install_' + edit_id).html());
								$('#room_id').val($('#hiden_room_id_' + edit_id).val());
								$('#user_id').val($('#hiden_user_id_' + edit_id).val());
								$('#status_id').val($('#hiden_status_id_' + edit_id).val());
								$('#suma').val($('#suma_' + edit_id).html());
								$('#amort').val($('#amort_' + edit_id).html());

							}
						$('#invent_modal').modal('show');
						$('#invent').select();
						$('#invent').focus();
					}
					else
					{
						if (selected_kt_row != 0)
							{
								if (edit_id == '0')
									{
										$('#kt_modalLabel').html('Додати техніку до Інв. ' + selected_invent_nom);
										$('#add_button').html('Додати техніку');
										$('#kt_top_name').html(selected_kt_name);
										$('#delete_button').hide();
										$('#edit_id').val('0');
										$('#invent_id').val(selected_kt_row);
										$('#invent_nom').val(selected_invent_nom);
									}
									else
									{
										$('#kt_modalLabel').html('Редагувати техніку');
										$('#add_button').html('Редагувати техніку');
										$('#kt_top_name').html(selected_kt_name);
										$('#delete_button').show();
										$('#edit_id').val(edit_id);
										$('#invent_id').val(selected_kt_row);
										$('#invent_nom').val(selected_invent_nom);
										$('#name').val($('#name_' + edit_id).html());
										$('#sn').val($('#sn_' + edit_id).html());
										$('#data_made').val($('#data_made_' + edit_id).html());
										$('#data_install').val($('#data_install_' + edit_id).html());
										$('#status_1_id').val($('#hiden_status_1_id_' + edit_id).val());
										$('#status_2_id').val($('#hiden_status_2_id_' + edit_id).val());
										$('#func').val($('#func_' + edit_id).html());
										$('#note').val($('#note_' + edit_id).html().replace(/<br>/g,'\n'));
										
										next_spec_row = 0;
										$('.spec_name_' + edit_id).map(function()
											{
												spec_add_row();
												$('#spec_name_' + next_spec_row).val($(this).html());
												$('#spec_value_' + next_spec_row).val($('#map_spec_value_' + edit_id + '_' + next_spec_row).html());
											}).get();
									}
							}

						if (selected_pz_row != 0)
							{
								if (edit_id == '0')
									{
										$('#pz_modalLabel').html('Додати ПЗ');
										$('#add_button').html('Додати ПЗ');
										$('#delete_button').hide();
										$('#edit_id').val('0');
									}
									else
									{
										$('#pz_modalLabel').html('Редагувати ПЗ');
										$('#add_button').html('Редагувати ПЗ');
										$('#delete_button').show();
										$('#edit_id').val(edit_id);
										next_pz_row = 0;
										$('.pz_name_' + edit_id).map(function()
											{
												spec_add_row();
												$('#pz_name_' + next_pz_row).val($(this).html());
												$('#pz_ver_' + next_pz_row).val($('#map_pz_ver_' + edit_id + '_' + next_pz_row).html());
											}).get();
									}
							}
					}
			}

		if (top_menu == 'rooms')
			{
				if (edit_id == '0')
					{
						$('#room_modalLabel').html('Додати кімнату');
						$('#add_button').html('Додати кімнату');
						$('#delete_button').hide();
						$('#edit_id').val('0');
					}
					else
					{
						$('#room_modalLabel').html('Редагувати кімнату');
						$('#add_button').html('Редагувати кімнату');
						$('#delete_button').show();
						$('#edit_id').val(edit_id);
						$('#nom').val($('#nom_' + edit_id).html());
						$('#name').val($('#name_' + edit_id).html());
						$('#name_full').val($('#name_full_' + edit_id).html());
					}
				$('#room_modal').modal('show');
			}

		if (top_menu == 'status')
			{
				if (edit_id == '0')
					{
						$('#status_modalLabel').html('Додати статус');
						$('#add_button').html('Додати статус');
						$('#delete_button').hide();
						$('#edit_id').val('0');
					}
					else
					{
						$('#status_modalLabel').html('Редагувати статус');
						$('#add_button').html('Редагувати статус');
						$('#delete_button').show();
						$('#edit_id').val(edit_id);
						$('#name').val($('#name_' + edit_id).html());
						$('#name_full').val($('#name_full_' + edit_id).html());
						$('#text_color').val($('#text_color_' + edit_id).html());
						$('#bg_color').val($('#bg_color_' + edit_id).html());
					}
				$('#status_modal').modal('show');
			}
	};

var next_spec_row = 0;
function spec_add_row()
	{
		hot_keys = true;
		next_spec_row++;
		var next_spec_tr = '<tr id="spec_tr_' + next_spec_row + '">' +
			'<td width="70%" id="spec_td1_' + next_spec_row + '">' +
				'<input id="spec_name_' + next_spec_row + '" NAME="spec_name[]" onkeyup="loadHelp(\'#spec_name_' + next_spec_row + '_help\', this.id, this.value)" type="text" class="form-control" value="" />' +
				'<div id="spec_name_' + next_spec_row + '_help" class="help_div"></div>' +
			'</td>' +
			'<td width="25%" id="spec_td2_' + next_spec_row + '">' +
				'<input id="spec_value_' + next_spec_row + '" NAME="spec_value[]" onkeyup="loadHelp(\'#spec_value_' + next_spec_row + '_help\', this.id, this.value)" type="text" class="form-control" value="" />' +
				'<div id="spec_value_' + next_spec_row + '_help" class="help_div"></div>' +
			'</td>' +
			'<td width="1" id="spec_td3_' + next_spec_row + '">' +
				'<button onclick="spec_del_row(\'spec_tr_' + next_spec_row + '\')" type="button" class="btn btn-danger">' +
					'<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>' +
				'</button>' +
			'</td>' +
		'<tr>';
		$('#spec_tbody').append(next_spec_tr);
	};

function spec_del_row(id)
	{
		hot_keys = true;
		$('#' + id).remove();
	};
	
var next_pz_row = 0;
function pz_add_row()
	{
		hot_keys = true;
		next_pz_row++;
		var next_pz_tr = '<tr id="pz_tr_' + next_pz_row + '">' +
			'<td width="60%" id="pz_td1_' + next_pz_row + '">' +
				'<input id="pz_name_' + next_pz_row + '" NAME="pz_name[]" onkeyup="loadHelp(\'#pz_name_' + next_pz_row + '_help\', this.id, this.value)" type="text" class="form-control" value="" />' +
				'<div id="pz_name_' + next_pz_row + '_help" class="help_div"></div>' +
			'</td>' +
			'<td width="10%" id="pz_td2_' + next_pz_row + '">' +
				'<input id="pz_ver_' + next_pz_row + '" NAME="pz_ver[]" onkeyup="loadHelp(\'#pz_ver_' + next_pz_row + '_help\', this.id, this.value)" type="text" class="form-control" value="" />' +
				'<div id="pz_ver_' + next_pz_row + '_help" class="help_div"></div>' +
			'</td>' +
			'<td width="10%" id="pz_td3_' + next_pz_row + '">' +
				'<input id="pz_data_' + next_pz_row + '" NAME="pz_data[]" onkeyup="loadHelp(\'#pz_data_' + next_pz_row + '_help\', this.id, this.value)" type="text" class="form-control" value="" />' +
				'<div id="pz_data_' + next_pz_row + '_help" class="help_div"></div>' +
			'</td>' +
			'<td width="20%" id="pz_td4_' + next_pz_row + '">' +
				'<input id="pz_lic_' + next_pz_row + '" NAME="pz_lic[]" onkeyup="loadHelp(\'#pz_lic_' + next_pz_row + '_help\', this.id, this.value)" type="text" class="form-control" value="" />' +
				'<div id="pz_lic_' + next_pz_row + '_help" class="help_div"></div>' +
			'</td>' +
			'<td width="1" id="pz_td5_' + next_pz_row + '">' +
				'<button onclick="pz_del_row(\'pz_tr_' + next_pz_row + '\')" type="button" class="btn btn-danger">' +
					'<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>' +
				'</button>' +
			'</td>' +
		'<tr>';
		$('#pz_tbody').append(next_pz_tr);
	};

function pz_del_row(id)
	{
		hot_keys = true;
		$('#' + id).remove();
	};

function add_id(a, edit_id)
	{
		if (a == 'kt')
			{
				var edit_id = $("#edit_id").val();
				var invent_id = $("#invent_id").val();
				var invent_nom = $("#invent_nom").val();
				var name = $("#name").val();
				var sn = $("#sn").val();
				var data_made = $("#data_made").val();
				var data_install = $("#data_install").val();
				var status_1_id = $("#status_1_id").val();
				var status_2_id = $("#status_2_id").val();
				var func = $("#func").val();
				var note = $("#note").val();

				var spec_name = $("input[name='spec_name\\[\\]']").map(function(){return $(this).val();}).get();
				var spec_value = $("input[name='spec_value\\[\\]']").map(function(){return $(this).val();}).get();

				$('#progress').show();
				$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'edit', edit_id: edit_id, invent_id: invent_id, invent_nom: invent_nom, name: name, sn: sn, data_made: data_made, data_install: data_install, status_1_id: status_1_id, status_2_id: status_2_id, func: func, note: note, spec_name: spec_name, spec_value: spec_value},
					function()
						{
							responceBlender();
						}
				);
			}

		if (a == 'pz')
			{
				$('#pz_modal').modal('hide');
				$('#progress').show();
				alert('ПЗ в розробці');
			}

		if (a == 'invent')
			{
				var edit_id = $("#edit_id").val();
				var name = $("#name").val();
				var invent = $("#invent").val();
				var inv_plus = $("#inv_plus").val();
				var data_made = $("#data_made").val();
				var data_install = $("#data_install").val();
				var status_id = $("#status_id").val();
				var room_id = $("#room_id").val();
				var user_id = $("#user_id").val();
				var suma = $("#suma").val();
				var amort = $("#amort").val();
				$('#invent_modal').modal('hide');
				$('#progress').show();
				$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'edit', edit_id: edit_id, name: name, invent: invent, inv_plus: inv_plus, data_made: data_made, data_install: data_install, status_id: status_id, room_id: room_id, user_id: user_id, suma: suma, amort: amort},
					function()
						{
							responceBlender();
						}
				);
			}

		if (a == 'rooms')
			{
				var edit_id = $("#edit_id").val();
				var nom = $("#nom").val();
				var name = $("#name").val();
				var name_full = $("#name_full").val();
				$('#room_modal').modal('hide');
				$('#progress').show();
				$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'edit', edit_id: edit_id, nom: nom, name: name, name_full: name_full},
					function()
						{
							responceBlender();
						}
				);
			}

		if (a == 'status')
			{
				var edit_id = $("#edit_id").val();
				var name = $("#name").val();
				var name_full = $("#name_full").val();
				var text_color = $("#text_color").val();
				var bg_color = $("#bg_color").val();
				$('#status_modal').modal('hide');
				$('#progress').show();
				$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'edit', edit_id: edit_id, name: name, name_full: name_full, text_color: text_color, bg_color: bg_color},
					function()
						{
							responceBlender();
						}
				);
			}
	};

function responceBlender()
	{
		hot_keys = true;
		$('#progress').hide();
		$('#informerLabel').html('Результат');
		$('#informer_footer').html('');
		$('#informer').modal('show');
	};

function delete_row_id()
	{
		if (top_menu == 'invent')
			{
				if (selected_row != 0)
					{
						if (selected_kt_row == 0 && selected_pz_row == 0)
							{
								delete_id(top_menu, selected_invent_nom, selected_row, '0');
							}
						if (selected_db_kt_id > 0)
							{
								delete_id('kt', selected_db_kt_name, selected_db_kt_id, '0');
							}
					}
			}
	}

function delete_id(a, name, edit_id, confirmed)
	{
		if (a == 'kt')
			{
				$('#invent_modal').modal('hide');
				if (confirmed == '0')
					{
						$('#informerLabel').html('Вилучення');
						$('#informer_body').html('Ви дійсно бажаєте вилучити ' + name);
						$('#informer_footer').html('<button onclick="delete_id(\'' + a + '\', \'' + encodeURIComponent(name) + '\', \'' + edit_id + '\', \'1\');" type="button" class="btn btn-danger">Так</button> <button type="button" class="btn btn-default" data-dismiss="modal">Ні</button>');
						$('#informer').modal('show');
					}
				if (confirmed == '1')
					{
						$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'delete', edit_id: edit_id, name: name},
							function()
								{
									responceBlender();
								}
						);
					}
			}
		
		if (a == 'invent')
			{
				$('#invent_modal').modal('hide');
				if (confirmed == '0')
					{
						$('#informerLabel').html('Вилучення');
						$('#informer_body').html('Ви дійсно бажаєте вилучити ' + name);
						$('#informer_footer').html('<button onclick="delete_id(\'' + a + '\', \'' + encodeURIComponent(name) + '\', \'' + edit_id + '\', \'1\');" type="button" class="btn btn-danger">Так</button> <button type="button" class="btn btn-default" data-dismiss="modal">Ні</button>');
						$('#informer').modal('show');
					}
				if (confirmed == '1')
					{
						$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'delete', edit_id: edit_id, name: name},
							function()
								{
									responceBlender();
								}
						);
					}
			}

		if (a == 'rooms')
			{
				$('#room_modal').modal('hide');
				if (confirmed == '0')
					{
						$('#informerLabel').html('Вилучення');
						$('#informer_body').html('Ви дійсно бажаєте вилучити ' + name);
						$('#informer_footer').html('<button onclick="delete_id(\'' + a + '\', \'' + encodeURIComponent(name) + '\', \'' + edit_id + '\', \'1\');" type="button" class="btn btn-danger">Так</button> <button type="button" class="btn btn-default" data-dismiss="modal">Ні</button>');
						$('#informer').modal('show');
					}
				if (confirmed == '1')
					{
						$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'delete', edit_id: edit_id, name: name},
							function()
								{
									responceBlender();
								}
						);
					}
			}

		if (a == 'status')
			{
				$('#status_modal').modal('hide');
				if (confirmed == '0')
					{
						$('#informerLabel').html('Вилучення');
						$('#informer_body').html('Ви дійсно бажаєте вилучити ' + name);
						$('#informer_footer').html('<button onclick="delete_id(\'' + a + '\', \'' + encodeURIComponent(name) + '\', \'' + edit_id + '\', \'1\');" type="button" class="btn btn-danger">Так</button> <button type="button" class="btn btn-default" data-dismiss="modal">Ні</button>');
						$('#informer').modal('show');
					}
				if (confirmed == '1')
					{
						$('#informer_body').load('jurnal_it_ajax.php', {menu: a, act: 'delete', edit_id: edit_id, name: name},
							function()
								{
									responceBlender();
								}
						);
					}
			}
	}

var hot_keys = true;
$(document).ready(function()
	{
		$('#progress').hide();
		$('#main_body').on('keydown', 'input', function (event)
		{
			if (event.which == 13)
				{
					var $this = $(event.target);
					var index = parseFloat($this.attr('data-index'));
					$('[data-index="' + (index + 1).toString() + '"]').select();
					$('[data-index="' + (index + 1).toString() + '"]').focus();
					event.preventDefault();
				}
		});

		$('html').on('keydown', '#main_body', function(event)
			{
				if (hot_keys)
					{
						if (event.keyCode == 45)
							{
								plus('0');
							}
						if (event.keyCode == 46)
							{
								delete_row_id();
							}
						if (event.keyCode == 119)
							{
								showKT();
							}
						if (event.keyCode == 113)
							{
								showPZ();
							}
						if (event.keyCode == 115)
							{
								print();
							}
						//event.preventDefault();
					}
			});
			
		$('html').on('hidden.bs.modal', '.modal', function ()
			{
				hot_keys = true;
			});

		show('invent');
		
	});
