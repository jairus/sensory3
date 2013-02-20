jQuery(function () { 
    
    jQuery("#xy_list").jqGrid({ 
        width : 1078,
        height : 320,
        url : DOCROOT +'rta/async_load_list/?t='+ (new Date).getTime(),
        postData : { ownlist : 1 },
        datatype : "json",
        colNames : ['#',
                    'Action',
                    'Status',
                    'Type of test',
                    'Samples name',
                    'Samples description',
                    'Requested by',
                    'Approved by',
                    'Approved schedule',
                    'Date filed',
                    'Location'
                   ],
        colModel : [{ name : 'count', index : 'count', width : 50, align : 'right', sortable : false, search : false },
                    { name : 'action', index : 'action', width : 80, align : 'center', sortable : false, resizable : false, search : false,
                      formatter : function(cellvalue, options, rowObject) {
                          
                          var action = '<a href="'+ DOCROOT +'rta/create/'+ cellvalue +'"><img title="re-use" src="'+ DOCROOT +'media/images/16x16/reuse.png" /></a>';
                          if(rowObject[2] == 'Pending') { action = '<a href="'+ DOCROOT +'rta/edit_by_owner/'+ cellvalue +'"><img title="edit" src="'+ DOCROOT +'media/images/16x16/edit.png" /></a>&nbsp;'+ action; }
                          
                          return action;
                      }
                    },
                    { name : 'state', index : 'state', width : 100, align : 'center', resizable : false,
                      search : true, stype : 'select',
                      searchoptions: { value : ':All;2:Approved;4:Pending;1:Cancelled' }
                    },
                    { name : 'type_of_test', index : 'type_of_test', width : 125, align : 'center', resizable : false, 
                      search : true, stype : 'select',
                      searchoptions: { 
                          value : ':All;affective:Affective;analytical:Analytical;micro:MICRO;physico_chem:Physico Chem'
                      }
                    },
                    { name : 'samples_name', index : 'samples_name', width : 140, search : true },
                    { name : 'samples desc', index : 'samples_desc', width : 160, sortable : false, search : false },                    
                    { name : 'requested_by', index : 'requested_by', width : 125, align : 'center', resizable : false, sortable : false },
                    { name : 'approved_by', index : 'approved_by', width : 125, align : 'center', resizable : false },
                    { name : 'schedule', index : 'schedule', width : 125, search : false },                    
                    { name : 'date filed', index : 'date_filed', width : 100, align : 'center', resizable : false, search : false },                    
                    { name : 'location', index : 'location', width : 90, align : "center" }
                   ],
        rowNum : 15,
        /*rowList : [10, 20, 30],*/
        pager : '#xy_list_pager',
        sortname : 'id',
        viewrecords : true,
        sortorder : "desc",
        caption : "Records",
        onSelectRow: function(id){
            //alert(id)
        }
    });


    jQuery("#xy_list")
        .jqGrid('navGrid', '#xy_list_pager', { edit : false, add : false, del : false, search : false })
        .jqGrid('filterToolbar',{ stringResult: true, searchOnEnter: true, defaultSearch : 'eq' });
        
    jQuery("input[id^='gs_']").css('height', '16px').css('font-size', '10px').watermark('Search');
    jQuery("#gs_state, #gs_type_of_test").css('height', '20px').css('font-size', '10px');
    
    if(window.location.href.indexOf('?pending') > -1) {
        
        setTimeout(function(){
            
            jQuery("#gs_state")[0].selectedIndex = 2;
            jQuery("#xy_list").jqGrid('setGridParam', { postData : { filters : '{"groupOp":"AND","rules":[{"field":"state","op":"eq","data":"4"}]}' }, search : true }).trigger("reloadGrid");
        }, 500);
    }
});