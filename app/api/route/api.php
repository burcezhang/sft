<?php
/**
 * FunAdmin
 * ============================================================================
 * 版权所有 2017-2028 FunAdmin，并保留所有权利。
 * 网站地址: http://www.FunAdmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp8实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/9/30
 */
use think\facade\Route;
////一般路由规则，访问的url为：v1/member/1,对应的文件为member类下的index方法
// Route::get(':version/member/index','api/:version.member/index');
Route::get(':version/index/category','api/:version.index/category');
Route::get(':version/index/index','api/:version.index/getindex');

// splash
Route::get(':version/index/splash','api/:version.index/splash');
// homedata
Route::get(':version/index/homedata','api/:version.index/homedata');

// deeproom
Route::get(':version/index/deeproom','api/:version.index/deeproom');

// datacenter
Route::get(':version/index/datacenter','api/:version.index/datacenter');

// stock
Route::get(':version/index/stock','api/:version.index/stock');

// sales
Route::get(':version/index/sales','api/:version.index/sales');

// daily
Route::get(':version/index/daily','api/:version.index/daily');

// dailydetail
Route::get(':version/index/dailydetail','api/:version.index/dailydetail');

// search
Route::get(':version/index/search','api/:version.index/search');

// details
Route::get(':version/index/details','api/:version.index/details');

// subscribe
Route::post(':version/index/subscribe','api/:version.index/subscribe');

// unsubscribe
Route::post(':version/index/unsubscribe','api/:version.index/unsubscribe');

// prices
Route::get(':version/index/prices','api/:version.index/prices');

// analysis
Route::get(':version/index/analysis','api/:version.index/analysis');

// sale
Route::get(':version/index/sale','api/:version.index/sale');

// history
Route::get(':version/index/history','api/:version.index/history');

// album
Route::get(':version/index/album','api/:version.index/album');

// login
Route::post(':version/index/login','api/:version.index/login');

// member
Route::get(':version/index/member','api/:version.index/member');

// upload
Route::post(':version/index/upload','api/:version.index/upload');

// edit
Route::post(':version/index/edit','api/:version.index/edit');

// bindphone
Route::post(':version/index/bindphone','api/:version.index/bindphone');

// protocol
Route::get(':version/index/protocol','api/:version.index/protocol');

// help
Route::get(':version/index/help','api/:version.index/help');

// tutorial
Route::get(':version/index/tutorial','api/:version.index/tutorial');

// datasync
Route::get(':version/datasync/region','api/:version.datasync/region');
Route::get(':version/datasync/house','api/:version.datasync/house');
Route::post(':version/datasync/synchouse','api/:version.datasync/synchouse');

Route::post(':version/datasync/househistory','api/:version.datasync/househistory');
Route::post(':version/datasync/synchousehistory','api/:version.datasync/synchousehistory');

// 一手房成交信息
Route::get(':version/house/houseDay','api/:version.house/houseDay');
Route::get(':version/house/houseMonth','api/:version.house/houseMonth');

// 一手商品房按面积统计成交信息
Route::get(':version/house/houseAreaDay','api/:version.house/houseAreaDay');
Route::get(':version/house/houseAreaMonth','api/:version.house/houseAreaMonth');

//首页最新成交数据信息
Route::get(':version/index/housedeal','api/:version.index/housedeal');

//首页最新成交数据信息
Route::get(':version/index/cyclerate','api/:version.index/cyclerate');

//首页各区住宅成交占比(近30天)
Route::get(':version/index/distribution','api/:version.index/distribution');

// //
// ////资源路由，详情查看tp手册资源路由
// //Route::resource(':version/member','api/:version.member');
// //
// ////生成access_token，post访问Token类下的token方法
// Route::post(':version/token','api/:version.token/build');
// Route::post(':version/token/refresh','api/:version.token/refresh');

