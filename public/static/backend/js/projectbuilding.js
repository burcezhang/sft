define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'ProjectBuilding/index'+location.search,
                    add_url:'ProjectBuilding/add'+location.search,
                    edit_url:'ProjectBuilding/edit'+location.search,
                    destroy_url:'ProjectBuilding/destroy'+location.search,
                    delete_url:'ProjectBuilding/delete'+location.search,
                    recycle_url:'ProjectBuilding/recycle'+location.search,
                    import_url:'ProjectBuilding/import'+location.search,
                    export_url:'ProjectBuilding/export'+location.search,
                    modify_url:'ProjectBuilding/modify'+location.search,

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
                    {field:'project', title: __('Project'),align: 'center'},
                    {field:'name', title: __('Name'),align: 'center'},
                    {field:'mainbuilding_attribute', title: __('MainbuildingAttribute'),align: 'center'},
                    {field:'mainbuilding_structure', title: __('MainbuildingStructure'),align: 'center'},
                    {field:'rptid', title: __('Rptid'),align: 'center'},
                    {field:'openid', title: __('Openid'),align: 'center'},
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
                    delete_url:'ProjectBuilding/delete'+location.search,
                    recycle_url:'ProjectBuilding/recycle'+location.search,
                    restore_url:'ProjectBuilding/restore'+location.search,
                    
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
                    {field:'project', title: __('Project'),align: 'center'},
                    {field:'name', title: __('Name'),align: 'center'},
                    {field:'mainbuilding_attribute', title: __('MainbuildingAttribute'),align: 'center'},
                    {field:'mainbuilding_structure', title: __('MainbuildingStructure'),align: 'center'},
                    {field:'rptid', title: __('Rptid'),align: 'center'},
                    {field:'openid', title: __('Openid'),align: 'center'},
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
