<?php

if (!function_exists('getSystemTable')) {
    /**
     * @param $table
     * @param $shift
     * @return array|string[]
     * 获取系统表格
     */
    function getSystemTable($table=[],$shift= [])
    {
        $tableList =  [
            'addon',
            'admin',
            'admin_log',
            'attach',
            'attach_group',
            'auth_group',
            'auth_rule',
            'blacklist',
            'builder',
            'builder_dict',
            'builder_dict_value',
            'builder_field',
            'builder_with',
            'config',
            'config_group',
            'field_type',
            'field_verify',
            'languages',
            'member',
            'member_account',
            'member_address',
            'member_group',
            'member_level',
            'member_third',
            'oauth2_access_token',
            'oauth2_client',
            'provinces',
        ];
        if(!empty($table)){
            $tableList =  array_merge($tableList,$table);
        }
        if(!empty($shift)){
            $tableList = array_diff($tableList, $shift);
        }
        return $tableList;
    }
}