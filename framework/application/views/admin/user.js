jQuery(function () { 
    
    jQuery("#xy_list").jqGrid({ 
        width : jQuery('#xy_list').parents('div').width() - 2,
        height : 320,
        url : DOCROOT +'admin/async_user_list/?t='+ (new Date).getTime(),
        datatype : "json",
        colNames : ['#',
                    'Action',
                    'Employee #',
                    'Level',
                    'Name',
                    'Username',
                    'Birthdate',
                    'Password',
                    'Department'
                   ],
        colModel : [{ name : '', index : '', width : 50, fixed : true, align : 'right', sortable : false, resizable : false, search : false },
                    { name : '', index : '', width : 60, fixed : true, align : 'center', sortable : false, resizable : false, search : false,
                      formatter : function(cellvalue, options, rowObject) {
                          //console.log(rowObject); DOCROOT +'admin/user_edit/'+ cellvalue
                          return '<a href="javascript:ADMIN.do_user_ae('+ cellvalue +')"><img title="edit" src="'+ DOCROOT +'media/images/16x16/edit.png" /></a>' +
                              '&nbsp;<a href="javascript:ADMIN.do_user_delete(0,\''+ rowObject[4] +'\')"><img title="delete" src="'+ DOCROOT +'media/images/16x16/delete.png" /></a>';
                      }
                    },
                    { name : 'employee_no', index : 'employee_no', width : 100, fixed : true, align : 'right', sortable : true, search : false },
                    { name : 'level', index : 'level', width : 150, fixed : true, sortable : false, resizable : false,
                      search : true, stype : 'select',
                      searchoptions: {
                          value : ':All;1:Administrator;2:Project Owner;3:Employee;4:Immediate Superior;5:Non-employee;6:Multi Level (2);7:Multi Level (3)'
                      }
                    },
                    { name : 'name', index : 'name', width : 250, fixed : true, sortable : true },
                    { name : 'username', index : 'username', width : 120, fixed : true, sortable : true },
                    { name : 'birthdate', index : 'birthdate', width : 80, fixed : true, align : 'center', sortable : false, search : false, resizable : false },
                    { name : 'password', index : 'password', width : 80, fixed : true, sortable : false, search : false },
                    { name : 'department', index : 'department', width : 150, fixed : true, sortable : false, search : false }
                   ],
        rowNum : 15,
        pager : '#xy_list_pager',
        sortname : 'lastname',
        viewrecords : true,
        sortorder : "asc",
        caption : "Records",
        onSelectRow: function(id){ ADMIN.user_id = id; }
    });

    jQuery("#xy_list")
        .jqGrid('navGrid', '#xy_list_pager', { edit : false, add : false, del : false, search : false })
        .jqGrid('filterToolbar',{ stringResult: true, searchOnEnter: true, defaultSearch : 'eq' });
        
    jQuery("input[id^='gs_']").css('height', '16px').css('font-size', '10px').watermark('Search');
    jQuery("#gs_level").css('height', '20px').css('font-size', '10px');
    
    jQuery('#user_add_trigger').click(function(){ ADMIN.do_user_ae(); });
});

