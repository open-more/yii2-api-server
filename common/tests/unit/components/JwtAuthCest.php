<?php
namespace common\tests\components;
use common\components\JwtAuth;
use common\tests\UnitTester;
use Yii;

class JwtAuthCest
{
    /** @var  JwtAuth */
    public $auth;
    public $tokenObj;
    public function _before(UnitTester $I)
    {
        $this->auth = new JwtAuth();
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testScope(UnitTester $I)
    {
        $token = $this->auth->createAccessToken()['token'];
        $I->am('user');
        $I->wantTo("test create AccessToken default scope");
        $I->assertNotEmpty($token);
        $I->expect(JwtAuth::SCOPE_APP === $this->auth->getScopeFromToken($token));
    }

    public function testCheckGetNonce(UnitTester $I)
    {
        $I->am('user');
        $I->wantTo("test Check GetNonce");

        $nonce1 = $this->auth->getNonce(6);
        $nonce2 = $this->auth->getNonce(6);
        $I->assertNotEquals($nonce1, $nonce2);
        $nonce3 = $this->auth->getNonce(12);
        $I->assertEquals(12, strlen($nonce3));
    }

    public function testOtherScope(UnitTester $I)
    {
        $id = 123;
        $scope = JwtAuth::SCOPE_FRONTEND;
        $tokenObj = $this->auth->createAccessToken($id, $scope);
        $I->am('user');
        $I->wantTo("test create AccessToken");
        $I->assertNotEmpty($tokenObj);
        $I->expect($this->auth->expire === $this->auth->getScopeFromToken($tokenObj['token']));
        $I->expect($scope === $this->auth->getScopeFromToken($tokenObj['token']));
        $I->expect($id === $this->auth->getIdFromToken($tokenObj['token']));
        $I->expectException('common\exceptions\Exception', function(){
            $this->auth->createAccessToken(123, 'NOT exist');
        });
    }


    public function testBanToken(UnitTester $I)
    {
        if(isset(Yii::$app->cache)){

            $id = 123;
            $token = $this->auth->createAccessToken($id)['token'];
            $I->am('user');
            $I->wantTo("test ban token");
            $I->assertNotEmpty($token);
            $I->expect($id === $this->auth->getIdFromToken($token));
            $this->auth->banToken($token);
            $this->auth->getIdFromToken($token);;
        }
    }


    public function testGetIdFromTokenWithDiffKey(UnitTester $I)
    {
        $id = 123;
        $this->tokenObj = $this->auth->createAccessToken($id);
        $I->am('user');
        $I->wantTo("test Get Id From Token with diff key");
        $I->assertNotEmpty($this->tokenObj);
        $this->auth->key = 'www.51kaifa.org';
        $I->expectException('common\exceptions\Exception', function(){
            $this->auth->getIdFromToken($this->tokenObj['token']);
        });
    }


    public function testGetIdFromTokenExpire(UnitTester $I)
    {
        $id = 123;
        $I->am('user');
        $I->wantTo("test Get Id From Token expired");

        $this->auth->expire = 1;
        $this->tokenObj = $this->auth->createAccessToken($id);
        sleep(2);
        $I->assertNotEmpty($this->tokenObj);
        $I->expectException('common\exceptions\Exception', function(){
            $this->auth->getIdFromToken($this->tokenObj['token']);
        });
    }

    public function testGetIdFromTokenRefresh(UnitTester $I)
    {
        $id = 123;
        $I->am('user');
        $I->wantTo("test Get Id From Token need refresh");

        $this->auth->refresh = 1;
        $this->tokenObj = $this->auth->createAccessToken($id);
        sleep(2);
        $I->assertNotEmpty($this->tokenObj);
        $I->expectException('common\exceptions\Exception', function(){
            $this->auth->getIdFromToken($this->tokenObj['token']);
        });
    }


    public function testGetGuestIdFromToken(UnitTester $I)
    {
        $id = 123;
        $I->am('user');
        $I->wantTo("test Get GuestId From Token");

        $this->tokenObj = $this->auth->createAccessToken($id);
        $I->assertNotEmpty($this->tokenObj);
        $I->assertContains(time() . "", $this->auth->getGuestIdFromToken($this->tokenObj['token']));
    }

    public function testCheckRefreshToken(UnitTester $I)
    {
        $id = 123;
        $I->am('user');
        $I->wantTo("test Check Refresh Token");

        $this->tokenObj = $this->auth->createAccessToken($id);
        $I->assertNotEmpty($this->tokenObj);
        $newToken = $this->auth->checkAndRefreshToken($this->tokenObj['token'], JwtAuth::SCOPE_APP, $id);
        $I->expect($this->auth->getIdFromToken($newToken['token']) === $this->auth->getIdFromToken($this->tokenObj['token']));
    }


    public function testCheckRefreshToken2(UnitTester $I)
    {
        $id = 123;
        $I->am('user');
        $I->wantTo("test Check Refresh Token2");

        $this->tokenObj = $this->auth->createAccessToken($id);
        $I->assertNotEmpty($this->tokenObj);
        $newToken = $this->auth->refreshToken($this->tokenObj['token']);
        $I->expect($this->auth->getIdFromToken($newToken['token']) === $this->auth->getIdFromToken($this->tokenObj['token']));
    }

}
