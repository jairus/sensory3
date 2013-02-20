/* ct_main.js
 * create_test's main js
 **/
jQuery(function () { 
    
    jQuery("#xy_list").jqGrid({ 
        width : jQuery('#xy_list').parents('div').width(),
        height : 320,
        url : DOCROOT +'sensory/async_q_list/?t='+ (new Date).getTime(),
        datatype : "json",
        colNames : ['#',
                    'Action',
                    'Name',
                    'Type of Test',
                    'Specifics',
                    'Schedule',
                    'Status',
                    'Date created'
                   ],
        colModel : [{ name : 'count', index : 'count', width : 50, fixed : true, align : 'right', sortable : false, search : false },
                    { name : 'action', index : 'action', width : 50, fixed : true, align : 'center', sortable : false, resizable : false, search : false,
                      formatter : function(cellvalue, options, rowObject) {
                          
                          return '<a href="'+ DOCROOT +'sensory/create_test/'+ cellvalue +'"><img title="edit" src="'+ DOCROOT +'media/images/16x16/edit.png" /></a>';
                      }
                    },
                    
                    { name : 'name', index : 'name', width : 300, fixed : true, search : true },
                    { name : 'type_of_test', index : 'type_of_test', width : 100, fixed : true, resizable : false,
                      search : true, stype : 'select',
                      searchoptions: { 
                          value : ':All;affective:Affective;analytical:Analytical;micro:MICRO;physico_chem:Physico Chem'
                      }
                    },
                    { name : '', index : '', width : 200, sortable : false, search : false },
                    { name : 'schedule', index : 'schedule', width : 200, sortable : false,
                        searchoptions: {
                            dataInit : function(el){
                                jQuery(el).datepicker(
                                    { 
                                        dateFormat : 'mm/dd/yy',
                                        onSelect: function(dateText, inst){ jQuery("#xy_list")[0].triggerToolbar(); }
                                    }
                                );
                            }
                        }
                    },
                    { name : 'status', index : 'status', width : 60, fixed : true, align : 'center', sortable : false, search : false },
                    { name : 'created', index : 'created', width : 140, fixed : true, search : false }
                    
                   ],
        rowNum : 15,
        pager : '#xy_list_pager',
        sortname : 'name',
        viewrecords : true,
        sortorder : "desc",
        caption : "Records",
        onSelectRow: function(id){},
        afterInsertRow : function(id, row) { if(row.status == "Canceled") { jQuery(this).jqGrid('setRowData', id, false, 'row_bg_red'); } }
    });

    jQuery("#xy_list")
        .jqGrid('navGrid', '#xy_list_pager', { edit : false, add : false, del : false, search : false })
        .jqGrid('filterToolbar',{ stringResult: true, searchOnEnter: true, defaultSearch : 'eq' });
        
    jQuery("input[id^='gs_']").css('height', '16px').css('font-size', '10px').watermark('Search');
    jQuery("#gs_type_of_test").css('height', '20px').css('font-size', '10px');    
    jQuery('#ui-datepicker-div').css('font-size', '12px');
    jQuery('#gs_schedule').watermark('mm/dd/yyyy');
});