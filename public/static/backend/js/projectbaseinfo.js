define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'ProjectBaseInfo/index'+location.search,
                    add_url:'ProjectBaseInfo/add'+location.search,
                    edit_url:'ProjectBaseInfo/edit'+location.search,
                    destroy_url:'ProjectBaseInfo/destroy'+location.search,
                    delete_url:'ProjectBaseInfo/delete'+location.search,
                    recycle_url:'ProjectBaseInfo/recycle'+location.search,
                    import_url:'ProjectBaseInfo/import'+location.search,
                    export_url:'ProjectBaseInfo/export'+location.search,
                    modify_url:'ProjectBaseInfo/modify'+location.search,

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
                    {field:'parcel_no', title: __('ParcelNo'),align: 'center'},
                    {field:'parcel_code', title: __('ParcelCode'),align: 'center'},
                    {field:'site_address', title: __('SiteAddress'),align: 'center'},
                    {field:'sr_date',title: __('SrDate'),align: 'center',dateformat:'yyyy-MM-dd',searchdateformat:'yyyy-MM-dd',search:'timerange',templet: Table.templet.time,sort:true},
                    {field:'zone', title: __('Zone'),align: 'center'},
                    {field:'contract_type', title: __('ContractType'),align: 'center'},
                    {field:'approvor', title: __('Approvor'),align: 'center'},
                    {field:'full_contract_no', title: __('FullContractNo'),align: 'center'},
                    {field:'lu_term',title: __('LuTerm'),align: 'center'},
                    {field:'lu_function', title: __('LuFunction'),align: 'center'},
                    {field:'land_grade', title: __('LandGrade'),align: 'center'},
                    {field:'basearea',title: __('Basearea'),align: 'center'},
                    {field:'lu_area',title: __('LuArea'),align: 'center'},
                    {field:'floor_area',title: __('FloorArea'),align: 'center'},
                    {field:'total_amount',title: __('TotalAmount'),align: 'center'},
                    {field:'land_grant_cost',title: __('LandGrantCost'),align: 'center'},
                    {field:'land_attach_cost',title: __('LandAttachCost'),align: 'center'},
                    {field:'land_devel_cost',title: __('LandDevelCost'),align: 'center'},
                    {field:'contract_flag', title: __('ContractFlag'),align: 'center'},
                    {field:'cert_no', title: __('CertNo'),align: 'center'},
                    {field:'projectwatcher', title: __('Projectwatcher'),align: 'center'},
                    {field:'housewatcher', title: __('Housewatcher'),align: 'center'},
                    {field:'housewatcher_price',title: __('HousewatcherPrice'),align: 'center'},
                    {field:'selltelephone1', title: __('Selltelephone1'),align: 'center'},
                    {field:'selltelephone2', title: __('Selltelephone2'),align: 'center'},
                    {field:'remark', title: __('Remark'),align: 'center'},
                    {field:'projectid',title: __('Projectid'),align: 'center'},
                    {field:'house_useage', title: __('HouseUseage'),align: 'center'},
                    {field:'ys_suites',title: __('YsSuites'),align: 'center'},
                    {field:'ys_area',title: __('YsArea'),align: 'center'},
                    {field:'xs_suites',title: __('XsSuites'),align: 'center'},
                    {field:'xs_area',title: __('XsArea'),align: 'center'},
                    {field:'landuse_demo', title: __('LanduseDemo'),align: 'center'},
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
                    delete_url:'ProjectBaseInfo/delete'+location.search,
                    recycle_url:'ProjectBaseInfo/recycle'+location.search,
                    restore_url:'ProjectBaseInfo/restore'+location.search,
                    
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
                    {field:'parcel_no', title: __('ParcelNo'),align: 'center'},
                    {field:'parcel_code', title: __('ParcelCode'),align: 'center'},
                    {field:'site_address', title: __('SiteAddress'),align: 'center'},
                    {field:'sr_date',title: __('SrDate'),align: 'center',dateformat:'yyyy-MM-dd',searchdateformat:'yyyy-MM-dd',search:'timerange',templet: Table.templet.time,sort:true},
                    {field:'zone', title: __('Zone'),align: 'center'},
                    {field:'contract_type', title: __('ContractType'),align: 'center'},
                    {field:'approvor', title: __('Approvor'),align: 'center'},
                    {field:'full_contract_no', title: __('FullContractNo'),align: 'center'},
                    {field:'lu_term',title: __('LuTerm'),align: 'center'},
                    {field:'lu_function', title: __('LuFunction'),align: 'center'},
                    {field:'land_grade', title: __('LandGrade'),align: 'center'},
                    {field:'basearea',title: __('Basearea'),align: 'center'},
                    {field:'lu_area',title: __('LuArea'),align: 'center'},
                    {field:'floor_area',title: __('FloorArea'),align: 'center'},
                    {field:'total_amount',title: __('TotalAmount'),align: 'center'},
                    {field:'land_grant_cost',title: __('LandGrantCost'),align: 'center'},
                    {field:'land_attach_cost',title: __('LandAttachCost'),align: 'center'},
                    {field:'land_devel_cost',title: __('LandDevelCost'),align: 'center'},
                    {field:'contract_flag', title: __('ContractFlag'),align: 'center'},
                    {field:'cert_no', title: __('CertNo'),align: 'center'},
                    {field:'projectwatcher', title: __('Projectwatcher'),align: 'center'},
                    {field:'housewatcher', title: __('Housewatcher'),align: 'center'},
                    {field:'housewatcher_price',title: __('HousewatcherPrice'),align: 'center'},
                    {field:'selltelephone1', title: __('Selltelephone1'),align: 'center'},
                    {field:'selltelephone2', title: __('Selltelephone2'),align: 'center'},
                    {field:'remark', title: __('Remark'),align: 'center'},
                    {field:'projectid',title: __('Projectid'),align: 'center'},
                    {field:'house_useage', title: __('HouseUseage'),align: 'center'},
                    {field:'ys_suites',title: __('YsSuites'),align: 'center'},
                    {field:'ys_area',title: __('YsArea'),align: 'center'},
                    {field:'xs_suites',title: __('XsSuites'),align: 'center'},
                    {field:'xs_area',title: __('XsArea'),align: 'center'},
                    {field:'landuse_demo', title: __('LanduseDemo'),align: 'center'},
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
