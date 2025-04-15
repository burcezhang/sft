<?php
/**
 * FunAdmin
 * ============================================================================
 * 版权所有 2017-2028 FunAdmin，并保留所有权利。
 * 网站地址: http://www.FunAdmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2017/8/2
 */
namespace app\swagger\controller;

use app\common\model\Member;
use fun\auth\Api;


class Index extends Api
{
    /**
     * @OA\Get(path="/swagger/Index/index",
     *   tags={"文章管理"},
     *   summary="文章列表",
     *   @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string", default="123456")),
     *   @OA\Parameter(name="page", in="query", description="页码", @OA\Schema(type="int", default="1")),
     *   @OA\Parameter(name="limit", in="query", description="行数", @OA\Schema(type="int", default="10")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function index()
    {
        $limit = $this->request->param('limit/d', 10);
        $data = Member::paginate($limit);
        $this->success('success', '', $data);
    }

    /**
     * @OA\Post(path="/swagger/index/save",
     *   tags={"文章管理"},
     *   summary="新增文章",
     *   @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *         @OA\Schema(
     *           @OA\Property(description="文章名称", property="username", type="string", default="dd"),
     *           @OA\Property(description="文章内容", property="content", type="string"),
     *           required={"title", "content"})
     *       )
     *     ),
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function save()
    {
        $param = $this->request->post();
        $rule = [
            'username' => 'require',
            'content' => 'require'
        ];

        $param = array_intersect_key($param, $rule);
        try {
            $this->validate($param, $rule);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }

        Member::create([
            'username' => $param['username'],
            'content' => $param['content'],
            'create_time' => time(),
        ]);

        $this->success('ok');
    }

    /**
     * @OA\Get(path="/swagger/index/read/{id}",
     *   tags={"文章管理"},
     *   summary="文章详情",
     *   @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string")),
     *   @OA\Parameter(name="id", in="path", description="文章id", @OA\Schema(type="int",default="1")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function read($id=1)
    {
        $data = Member::find($id);
        $this->success('success', '', $data);
    }

    /**
     * @OA\Put(path="/swagger/index/update/{id}",
     *   tags={"文章管理"},
     *   summary="编辑文章",
     *   @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string")),
     *   @OA\Parameter(name="id", in="path", description="文章id", @OA\Schema(type="int",default="1")),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="content-type/json",
     *         @OA\Schema(
     *           @OA\Property(description="名字", property="username", type="string"),
     *           @OA\Property(description="内容", property="content", type="string"),
     *           required={"username", "content"})
     *       )
     *     ),
     *   @OA\Response(response="200", description="successful operation")
     * )
     */
    public function update($id)
    {
        $param = $this->request->put();
        $rule = [
            'username' => 'require',
            'content' => 'require'
        ];

        $param = array_intersect_key($param, $rule);

        try {
            validate($param, $rule);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }

        Member::where('id', $id)->update([
            'username' => $param['username'],
            'update_time' => time(),
        ]);

        $this->success('ok');
    }

    /**
     * @OA\Delete(path="/swagger/index/delete/{id}",
     *   tags={"文章管理"},
     *   summary="删除文章",
     *   @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string")),
     *   @OA\Parameter(name="id", in="path", description="文章id", @OA\Schema(type="int",default="1")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function delete($id)
    {
        Member::destroy($id);
        $this->success('ok');
    }

}