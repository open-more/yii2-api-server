<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2017/4/1
 * Time: 下午2:07
 */

namespace backend\modules\v1\controllers;

use backend\biz\RbacLogic;
use backend\modules\v1\controllers\BaseController;
use common\exceptions\BaseException;
use common\services\RbacService;
use Yii;

class RbacController extends BaseController
{
    /** @var  RbacService */
    public $rbacService;
    public function init()
    {
        parent::init();
        $this->rbacService = Yii::$container->get(RbacService::class);
    }

    public function rbacCheckExcept()
    {
        return ['init', 'create-user-role'];
    }

    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $controller = $auth->createPermission('v1/rbac/*');
        $controller->description = '权限管理所有接口权限';
        $auth->add($controller);

        $guest = $auth->createRole('guest');
        $guest->description = '访客';
        $auth->add($guest);

        $guest = $auth->createRole('root');
        $guest->description = '超级管理员';
        $auth->add($guest);
        $auth->addChild($guest, $controller);

        $otherUser = $auth->createRole('other');
        $otherUser->description = '其它用户';
        $auth->add($otherUser);

        $admin = $auth->createRole('admin');
        $admin->description = '管理员';
        $auth->add($admin);
        $auth->addChild($admin, $controller);

        /*
         *
         * Permission -- admin -- other -- Guest --
         * index           x        x        x
         * create          x
         * update          x
         * delete          x
         * other           x        x
         */

        $auth->assign($admin, 1);
        return true;
    }


    public function actionIndex(){
        return 'index ok';
    }

    public function actionCreate(){
        return 'create ok';
    }

    public function actionDelete($id){
        return 'delete ok';
    }

    public function actionUpdate($id){
        return 'update ok';
    }

    public function actionOther(){
        return 'other ok';
    }

    /**
     * GET
     * 获得所有权限
     * @return \yii\rbac\Item[]
     */
    public function actionPermission(){
        return $this->rbacService->getPermission();
    }

    /**
     * POST
     * 创建权限
     * @return \yii\rbac\Item[]
     */
    public function actionCreatePermission(){
        $name = Yii::$app->request->getBodyParam('name', null);
        $desc = Yii::$app->request->getBodyParam('description', null);
        return $this->rbacService->createPermission($name, $desc);
    }

    /**
     * GET
     * 获得指定用户的所有action权限
     * @return \yii\rbac\Item[]
     */
    public function actionPermissionByUser($uid){
        return $this->rbacService->getPermissionByUser($uid);
    }

    /**
     * GET
     * 获得所有角色
     * @return \yii\rbac\Item[]
     */
    public function actionRole(){
        return $this->rbacService->getRole();
    }

    /**
     * POST
     * 创建角色
     * name 角色名
     * description 描述
     */
    public function actionCreateRole()
    {
        $roleName = Yii::$app->request->getBodyParam('name', null);
        $desc = Yii::$app->request->getBodyParam('description', null);
        return $this->rbacService->createRole($roleName, $desc);
    }

    /**
     * DELETE
     * 删除指定的角色
     * @param $name
     * @return mixed
     */
    public function actionDeleteRole($name)
    {
        return $this->rbacService->removeRole($name);
    }


    /**
     * GET
     * 获得指定角色的所有权限
     * @param $name
     * @return mixed
     */
    public function actionListRolePermission($name)
    {
        return $this->rbacService->getRolePermission($name);
    }

    /**
     * POST
     * 为角色指定权限
     */
    public function actionCreateRolePermission()
    {
        $perList = Yii::$app->request->getBodyParam('permissions', null);
        $roleName = Yii::$app->request->getBodyParam('role', null);
        return $this->rbacService->createRolePermission($perList, $roleName);
    }

    /**
     * DELETE
     * 删除指定角色的所有权限
     * @param $name
     * @return mixed
     */
    public function actionRemoveRolePermission($name)
    {
        return $this->rbacService->removeRolePermission($name);
    }

    /**
     * GET
     * 返回指定用户的角色,权限
     * @param $uid
     * @return \yii\rbac\Assignment[]
     */
    public function actionAssignment($uid){
        return $this->rbacService->getAssignment($uid);
    }

    /**
     * POST
     * 为用户指定角色
     * uid
     * role
     */
    public function actionCreateUserRole()
    {
        $uid = Yii::$app->request->getBodyParam('uid', null);
        $roleList = Yii::$app->request->getBodyParam('roles', null);
        return $this->rbacService->createUserRole($uid, $roleList);
    }

    /**
     * DELETE
     * 删除指定用户的所有角色
     * @param $uid
     * @return mixed
     */
    public function actionDeleteUserRole($uid)
    {
        return $this->rbacService->removeAssignment($uid);
    }
}