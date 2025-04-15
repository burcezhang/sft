define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'BuildingUnits/index'+location.search,
                    add_url:'BuildingUnits/add'+location.search,
                    edit_url:'BuildingUnits/edit'+location.search,
                    destroy_url:'BuildingUnits/destroy'+location.search,
                    delete_url:'BuildingUnits/delete'+location.search,
                    recycle_url:'BuildingUnits/recycle'+location.search,
                    import_url:'BuildingUnits/import'+location.search,
                    export_url:'BuildingUnits/export'+location.search,
                    modify_url:'BuildingUnits/modify'+location.search,

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
                    {field:'sellers', title: __('Sellers'),align: 'center'},
                    {field:'building_name', title: __('BuildingName'),align: 'center'},
                    {field:'building_branch', title: __('BuildingBranch'),align: 'center'},
                    {field:'floor', title: __('Floor'),align: 'center'},
                    {field:'house_nb', title: __('HouseNb'),align: 'center'},
                    {field:'usage', title: __('Usage'),align: 'center'},
                    {field:'ys_inside_area',title: __('YsInsideArea'),align: 'center'},
                    {field:'ys_expand_area',title: __('YsExpandArea'),align: 'center'},
                    {field:'ys_building_area',title: __('YsBuildingArea'),align: 'center'},
                    {field:'jg_inside_area',title: __('JgInsideArea'),align: 'center'},
                    {field:'jg_expand_area',title: __('JgExpandArea'),align: 'center'},
                    {field:'jg_building_area',title: __('JgBuildingArea'),align: 'center'},
                    {field:'ask_price_each_b',title: __('AskPriceEachB'),align: 'center'},
                    {field:'ask_price_total_b',title: __('AskPriceTotalB'),align: 'center'},
                    {field:'color', title: __('Color'),align: 'center'},
                    {field:'last_status_name', title: __('LastStatusName'),align: 'center'},
                    {field:'str_contract_id', title: __('StrContractId'),align: 'center',sort:true},
                    {field:'pre_sellId',title: __('PreSellId'),align: 'center'},
                    {field:'fybId',title: __('FybId'),align: 'center'},
                    {field:'ysProjectId',title: __('YsProjectId'),align: 'center'},
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
                    delete_url:'BuildingUnits/delete'+location.search,
                    recycle_url:'BuildingUnits/recycle'+location.search,
                    restore_url:'BuildingUnits/restore'+location.search,
                    
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
                    {field:'sellers', title: __('Sellers'),align: 'center'},
                    {field:'building_name', title: __('BuildingName'),align: 'center'},
                    {field:'building_branch', title: __('BuildingBranch'),align: 'center'},
                    {field:'floor', title: __('Floor'),align: 'center'},
                    {field:'house_nb', title: __('HouseNb'),align: 'center'},
                    {field:'usage', title: __('Usage'),align: 'center'},
                    {field:'ys_inside_area',title: __('YsInsideArea'),align: 'center'},
                    {field:'ys_expand_area',title: __('YsExpandArea'),align: 'center'},
                    {field:'ys_building_area',title: __('YsBuildingArea'),align: 'center'},
                    {field:'jg_inside_area',title: __('JgInsideArea'),align: 'center'},
                    {field:'jg_expand_area',title: __('JgExpandArea'),align: 'center'},
                    {field:'jg_building_area',title: __('JgBuildingArea'),align: 'center'},
                    {field:'ask_price_each_b',title: __('AskPriceEachB'),align: 'center'},
                    {field:'ask_price_total_b',title: __('AskPriceTotalB'),align: 'center'},
                    {field:'color', title: __('Color'),align: 'center'},
                    {field:'last_status_name', title: __('LastStatusName'),align: 'center'},
                    {field:'str_contract_id', title: __('StrContractId'),align: 'center',sort:true},
                    {field:'pre_sellId',title: __('PreSellId'),align: 'center'},
                    {field:'fybId',title: __('FybId'),align: 'center'},
                    {field:'ysProjectId',title: __('YsProjectId'),align: 'center'},
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
