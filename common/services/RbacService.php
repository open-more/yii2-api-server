<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 2017/4/22
 * Time: 下午1:55
 */

namespace common\services;


use common\exceptions\Exception;
use Yii;
use yii\base\Model;
use yii\rbac\Assignment;
use yii\rbac\BaseManager;
use yii\rbac\DbManager;
use yii\rbac\Item;
use yii\rbac\Role;

class RbacService extends Model
{
    /** @var  DbManager */
    public $rbacManager;
    public function init()
    {
        parent::init();
        $this->rbacManager = Yii::$app->authManager;
    }

    /**
     * 创建权限
     * @param $name
     * @param $desc
     * @return \yii\rbac\Permission
     */
    public function createPermission($name, $desc){
        $per = $this->rbacManager->createPermission($name);
        $per->description = $desc;
        return $per;
    }

    /**
     * 获得所有权限
     * @return \yii\rbac\Item[]
     */
    public function getPermission(){
        return $this->rbacManager->getPermissions();
    }

    /**
     * GET
     * 获得指定用户的所有action权限
     * @return \yii\rbac\Item[]
     * @param $uid
     * @return array|\yii\rbac\Permission[]
     */
    public function getPermissionByUser($uid){
        return $this->rbacManager->getPermissionsByUser($uid);
    }

    /**
     * 获得所有角色
     * @return \yii\rbac\Item[]
     */
    public function getRole(){
        return $this->rbacManager->getRoles();
    }

    /**
     * 创建角色
     * @param $name
     * @param $desc
     * @return \yii\rbac\Role
     * @throws Exception
     */
    public function createRole($name, $desc)
    {
        if(!isset($name) ||
            !isset($desc)){
            throw new Exception('缺少必须的参数: name和description');
        }

        $item = $this->rbacManager->getRole($name);
        if($item){
            throw new Exception("角色: {$name}已经存在");
        }

        $role = $this->rbacManager->createRole($name);
        $role->description = $desc;
        $this->rbacManager->add($role);
        return $role;
    }

    /**
     * 删除指定的角色
     * @param $name
     * @return string
     * @throws Exception
     */
    public function removeRole($name)
    {
        $item = $this->rbacManager->getRole($name);
        if(empty($item)){
            throw new Exception("找不到角色: {$name}");
        }
        $this->rbacManager->remove($item);
        return true;
    }

    /**
     * 获得指定角色的所有权限
     * @param $name
     * @return \yii\rbac\Permission[]
     * @throws Exception
     */
    public function getRolePermission($name)
    {
        $roleObj = $this->rbacManager->getRole($name);
        if(empty($roleObj)){
            throw new Exception("无法找到角色: {$name}");
        }
        return $this->rbacManager->getPermissionsByRole($name);
    }

    /**
     * 为角色指定权限
     * @param $permissions
     * @param $roleName
     * @return bool
     * @throws Exception
     */
    public function createRolePermission($permissions, $roleName)
    {
        if(!isset($permissions) ||
            !isset($roleName)){
            throw new Exception('缺少必须的参数: permissions和role');
        }

        $roleObj = $this->rbacManager->getRole($roleName);
        if(empty($roleObj)){
            throw new Exception("无法找到角色: {$roleName}");
        }
        // 先删除当前角色所有权限,再添加
        $this->rbacManager->removeChildren($roleObj);

        foreach ($permissions as $per){
            $perObj = $this->rbacManager->getPermission($per);
            if(empty($perObj)){
                throw new Exception("无法找到权限: {$per}");
            }
            if($this->rbacManager->hasChild($roleObj, $perObj)){
                throw new Exception("角色: {$roleName}已经拥有权限{$per}");
            }
            if(!$this->rbacManager->addChild($roleObj, $perObj)){
                throw new Exception("添加权限失败: {$per}");
            }
        }
        return true;
    }

    /**
     * 删除指定角色的所有权限
     * @param $name
     * @return bool
     * @throws Exception
     */
    public function removeRolePermission($name)
    {
        $roleObj = $this->rbacManager->getRole($name);
        if(empty($roleObj)){
            throw new Exception("无法找到角色: {$name}");
        }
        return $this->rbacManager->removeChildren($roleObj);
    }

    /**
     * 返回指定用户的角色,权限
     * @param $uid
     * @return \yii\rbac\Assignment[]
     */
    public function getAssignment($uid){
        return $this->rbacManager->getAssignments($uid);
    }

    /**
     * 为用户指定角色列表
     * @param $uid
     * @param $roleNameList
     * @return string
     * @throws Exception
     */
    public function createUserRole($uid, $roleNameList)
    {
        if(!isset($uid) ||
            !isset($roleNameList)){
            throw new Exception('缺少必须的参数: uid和roles');
        }

        // 先删除当前用户的所有角色,再添加
        $this->rbacManager->revokeAll($uid);

        foreach ($roleNameList as $name){
            /** @var Role $role */
            $role = $this->rbacManager->getRole($name);
            if(empty($role)){
                throw new Exception("无法找到角色: {$name}");
            }
            if(!$this->rbacManager->assign($role, $uid)){
                throw new Exception("无法为用户{$uid}盏角色: {$name}");
            }

        }
        return true;
    }

    /**
     * 删除指定用户的所有角色
     * @param $uid
     * @return string
     * @throws Exception
     */
    public function removeAssignment($uid)
    {
        $itemArray = $this->rbacManager->getAssignments($uid);
        foreach ($itemArray as $user_name => $assignment){
            /** @var Assignment $assignment */
            $role = $this->rbacManager->getRole($assignment->roleName);
            if(empty($role)){
                throw new Exception("无法找到角色: {$assignment}");
            }
            $this->rbacManager->revoke($role, $uid);
        }
        return true;
    }
}