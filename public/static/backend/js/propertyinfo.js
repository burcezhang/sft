define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'PropertyInfo/index'+location.search,
                    add_url:'PropertyInfo/add'+location.search,
                    edit_url:'PropertyInfo/edit'+location.search,
                    destroy_url:'PropertyInfo/destroy'+location.search,
                    delete_url:'PropertyInfo/delete'+location.search,
                    recycle_url:'PropertyInfo/recycle'+location.search,
                    import_url:'PropertyInfo/import'+location.search,
                    export_url:'PropertyInfo/export'+location.search,
                    modify_url:'PropertyInfo/modify'+location.search,

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
                    {field:'syp_id', title: __('SypId'),align: 'center',sort:true},
                    {field:'sype_id', title: __('SypeId'),align: 'center',sort:true},
                    {field:'image_path',title: __('ImagePath'),templet: Table.templet.url},
                    {field:'project', title: __('Project'),align: 'center'},
                    {field:'strpreprojectid', title: __('Strpreprojectid'),align: 'center'},
                    {field:'site_address', title: __('SiteAddress'),align: 'center'},
                    {field:'zone', title: __('Zone'),align: 'center'},
                    {field:'proj_useage', title: __('ProjUseage'),align: 'center'},
                    {field:'build_area_str', title: __('BuildAreaStr'),align: 'center'},
                    {field:'average_price',title: __('AveragePrice'),align: 'center'},
                    {field:'ys_date_str', title: __('YsDateStr'),align: 'center'},
                    {field:'organ_name', title: __('OrganName'),align: 'center'},
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
                    delete_url:'PropertyInfo/delete'+location.search,
                    recycle_url:'PropertyInfo/recycle'+location.search,
                    restore_url:'PropertyInfo/restore'+location.search,
                    
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
                    {field:'syp_id', title: __('SypId'),align: 'center',sort:true},
                    {field:'sype_id', title: __('SypeId'),align: 'center',sort:true},
                    {field:'image_path',title: __('ImagePath'),templet: Table.templet.url},
                    {field:'project', title: __('Project'),align: 'center'},
                    {field:'strpreprojectid', title: __('Strpreprojectid'),align: 'center'},
                    {field:'site_address', title: __('SiteAddress'),align: 'center'},
                    {field:'zone', title: __('Zone'),align: 'center'},
                    {field:'proj_useage', title: __('ProjUseage'),align: 'center'},
                    {field:'build_area_str', title: __('BuildAreaStr'),align: 'center'},
                    {field:'average_price',title: __('AveragePrice'),align: 'center'},
                    {field:'ys_date_str', title: __('YsDateStr'),align: 'center'},
                    {field:'organ_name', title: __('OrganName'),align: 'center'},
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
