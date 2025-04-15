define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'MoneyWatcher/index'+location.search,
                    add_url:'MoneyWatcher/add'+location.search,
                    edit_url:'MoneyWatcher/edit'+location.search,
                    destroy_url:'MoneyWatcher/destroy'+location.search,
                    delete_url:'MoneyWatcher/delete'+location.search,
                    recycle_url:'MoneyWatcher/recycle'+location.search,
                    import_url:'MoneyWatcher/import'+location.search,
                    export_url:'MoneyWatcher/export'+location.search,
                    modify_url:'MoneyWatcher/modify'+location.search,

                }
            }
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                primaryKey:'id',
                toolbar: ['refresh','add','delete','import','export','recycle'],
                cols: [[
                    {checkbox: true,},
                     {field: 'id', title: __('ID'), sort:true,},
                    {field:'fyp_id', title: __('FypId'),align: 'center',sort:true},
                    {field:'moneywatcher', title: __('Moneywatcher'),align: 'center'},
                    {field:'moneywatcher_name', title: __('MoneywatcherName'),align: 'center'},
                    {field:'moneywatcher_id', title: __('MoneywatcherId'),align: 'center',sort:true},
                    {field:'credit_no', title: __('CreditNo'),align: 'center'},
                    {field:'pre_sellId',title: __('PreSellId'),align: 'center'},
                    {field:'status',search: 'select',title: __('Status'),filter: 'status',selectList:statusList,templet: Table.templet.select},
                    {field:'create_time',title: __('CreateTime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'timerange',templet: Table.templet.time,sort:true,searchOp:'daterange'},
                    {
                        minWidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["edit", "destroy","delete"]
                    },
                ]],
                limits: [10, 15, 20, 25, 50, 100,500],
                limit: 15,
                page: true,
                done: function (res, curr, count) {
                }
            });
            Table.api.bindEvent(Table.init.tableId);
        },
        add: function () {
            Controller.api.bindevent()
        },
        edit: function () {
            Controller.api.bindevent()
        },
        recycle: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests: {
                    delete_url:'MoneyWatcher/delete'+location.search,
                    recycle_url:'MoneyWatcher/recycle'+location.search,
                    restore_url:'MoneyWatcher/restore'+location.search,
                    
                },
            };
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.recycle_url),
                init: Table.init,
                primaryKey:'id',
                toolbar: ['refresh','delete','restore'],
                cols: [[
                    {checkbox: true,},
                     {field: 'id', title: __('ID'), sort:true,},
                    {field:'fyp_id', title: __('FypId'),align: 'center',sort:true},
                    {field:'moneywatcher', title: __('Moneywatcher'),align: 'center'},
                    {field:'moneywatcher_name', title: __('MoneywatcherName'),align: 'center'},
                    {field:'moneywatcher_id', title: __('MoneywatcherId'),align: 'center',sort:true},
                    {field:'credit_no', title: __('CreditNo'),align: 'center'},
                    {field:'pre_sellId',title: __('PreSellId'),align: 'center'},
                    {field:'status',search: 'select',title: __('Status'),filter: 'status',selectList:statusList,templet: Table.templet.select},
                    {field:'create_time',title: __('CreateTime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'timerange',templet: Table.templet.time,sort:true,searchOp:'daterange'},
                    {
                        minWidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["restore","delete"]
                    },
                ]],
                limits: [10, 15, 20, 25, 50, 100,500],
                limit: 15,
                page: true,
                done: function (res, curr, count) {
                }
            });
            Table.api.bindEvent(Table.init.tableId);
        },

        api: {
            bindevent: function () {
                Form.api.bindEvent($('form'))
            }
        }
    };
    return Controller;
});
