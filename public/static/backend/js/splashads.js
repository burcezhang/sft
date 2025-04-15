define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'SplashAds/index'+location.search,
                    add_url:'SplashAds/add'+location.search,
                    edit_url:'SplashAds/edit'+location.search,
                    destroy_url:'SplashAds/destroy'+location.search,
                    delete_url:'SplashAds/delete'+location.search,
                    import_url:'SplashAds/import'+location.search,
                    export_url:'SplashAds/export'+location.search,
                    modify_url:'SplashAds/modify'+location.search,

                }
            }
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                primaryKey:'id',
                toolbar: ['refresh','add','destroy','import','export'],
                cols: [[
                    {checkbox: true,},
                     {field: 'id', title: __('ID'), sort:true,},
                    {field:'ad_name', title: __('AdName'),align: 'center'},
                    {field:'image_url',title: __('ImageUrl'),filter: 'image_url',templet: Table.templet.url},
                    {field:'redirect_url',title: __('RedirectUrl'),filter: 'redirect_url',templet: Table.templet.url},
                    {field:'status',search: 'select',title: __('Status'),filter: 'status',selectList:statusList,templet: Table.templet.select},
                    {field:'display_duration',title: __('DisplayDuration'),align: 'center'},
                    {field:'create_time',title: __('CreateTime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'timerange',templet: Table.templet.time,sort:true,searchOp:'daterange'},
                    {
                        minWidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat:["edit","destroy"]
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
        
        api: {
            bindevent: function () {
                Form.api.bindEvent($('form'))
            }
        }
    };
    return Controller;
});
