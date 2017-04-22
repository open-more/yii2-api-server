<?php
namespace v1;
use \ApiTester;
use Codeception\Util\HttpCode;
use common\activeRecords\Administrator;
use Yii;

class RbacCest
{
    public $token;
    public $rootToken;
    public $root;
    public $admin;
    public $rootRole = 'root';
    public $testRole = "testrole";
    public $testPerm = "testPermission";
    public function _before(ApiTester $I)
    {
        // 获得超级用户权限
        $this->admin = Administrator::findOne(2);
        $this->root = Administrator::findOne(1);
        $this->token = Yii::$app->auth->createAccessToken($this->admin->id)['token'];
        $this->rootToken = Yii::$app->auth->createAccessToken($this->root->id)['token'];
//        var_dump($this->token);die();
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function testOptions(ApiTester $I)
    {
        $I->am('普通用户');
        $I->wantTo('测试Options');
        $I->sendOPTIONS('rbac');
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseContains('OK');
    }


    /**
     * POST:rbac/role
     * 测试创建角色
     * @return array
     */
    public function testCreateRole(ApiTester $I)
    {
        $description = "test desc";
        $params = [
            "name" => $this->testRole,
            "description" => $description,
        ];

        $I->am('超级用户');
        $I->wantTo('测试创建角色');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendPOST('rbac/role', $params);
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContainsJson(
            [
                'name' => $this->testRole
            ]
        );
    }


    /**
     * GET:rbac/role'
     * 测试获得角色列表
     * @param ApiTester $I
     */
    public function testGetRoles(ApiTester $I)
    {
        $I->am('超级用户');
        $I->wantTo('测试获得角色列表');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendGET('rbac/role');
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
    }

    /**
     * DELETE:rbac/role?name=root
     * 测试删除角色
     * @param ApiTester $I
     */
    public function testDeleleRoles(ApiTester $I)
    {
        $I->am('超级用户');
        $I->wantTo('测试删除角色');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendDELETE("rbac/role?name={$this->rootRole}");
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
    }


    /**
     * POST:rbac/user-role
     * 测试为用户指定角色
     * @return mixed
     */
    public function testCreateUserRole(ApiTester $I)
    {
        $uid = $this->admin->id;
        $roleList[] = $this->rootRole;
        $params = [
            "uid" => $uid,
            "roles" => $roleList,
        ];

        $I->am('超级用户');
        $I->wantTo('测试为用户指定角色');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendPOST('rbac/user-role', $params);
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseEquals('true');
    }


    /**
     * DELETE:rbac/user-role?uid=1
     * 测试删除用户的所有角色
     * @return mixed
     */
    public function testDeleteUserRole(ApiTester $I)
    {
        $I->am('超级用户');
        $I->wantTo('测试删除用户的所有角色');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendDELETE("rbac/user-role?uid={$this->admin->id}");
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseEquals('true');
    }

    /**
     * POST:rbac/permission
     * 测试创建权限
     * @param ApiTester $I
     */
    public function testCreatePermission(ApiTester $I)
    {
        $params = [
            "name" => $this->testPerm,
            "desc" => 'test desc',
        ];

        $I->am('超级用户');
        $I->wantTo('测试创建权限');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendPOST('rbac/permission', $params);
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
    }



    /**
     * GET:rbac/permission
     * 测试获得权限
     * @param ApiTester $I
     */
    public function testGetPermissions(ApiTester $I)
    {
        $I->am('超级用户');
        $I->wantTo('测试获得权限');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendGET('rbac/permission');
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
    }

    /**
     * GET:rbac/role-permission?name=admin
     * 测试获得指定角色的所有权限
     */
    public function testGetRolePermission(ApiTester $I)
    {
        $I->am('超级用户');
        $I->wantTo('测试获得指定角色的所有权限');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendGET("rbac/role-permission?name=$this->rootRole");
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
    }

    /**
     * POST:rbac/role-permission
     * 测试为角色指定权限
     * @param ApiTester $I
     */
    public function testCreateRolePermission(ApiTester $I)
    {
        $permissions[] = 'v1/rbac/*';
        $params = [
            "permissions" => $permissions,
            "role" => $this->rootRole,
        ];

        $I->am('超级用户');
        $I->wantTo('测试为角色指定权限');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendPOST('rbac/role-permission', $params);
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseEquals('true');
    }

    /**
     * DELETE:rbac/role-permission?name=x
     * 测试删除指定角色的所有权限
     * @param ApiTester $I
     */
    public function testDeleteRolePermission(ApiTester $I)
    {
        $name = $this->rootRole;

        $I->am('超级用户');
        $I->wantTo('测试删除指定角色的所有权限');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendDELETE("rbac/role-permission?name={$name}");
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseEquals('true');
    }

    /**
     * 测试返回指定用户的角色,权限
     * @param ApiTester $I
     */
    public function testGetUserPermission(ApiTester $I)
    {
        $I->am('超级用户');
        $I->wantTo('测试返回指定用户的角色,权限');
        $I->amBearerAuthenticated($this->rootToken);
        $I->sendGET("rbac/assignment?uid={$this->admin->id}");
        $I->canSeeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseIsJson();
    }

    /**
     * 测试没有权限的访问
     * @param ApiTester $I
     */
    public function testOperationWithoutPerm(ApiTester $I)
    {
        $I->am('超级用户');
        $I->wantTo('测试没有权限的访问');
        $I->amBearerAuthenticated($this->token);
        $I->sendGET("rbac/assignment?uid={$this->admin->id}");
        $I->canSeeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->canSeeResponseIsJson();
        $I->canSeeResponseContainsJson(
            [
                "code" => 40101
            ]
        );
    }
}
