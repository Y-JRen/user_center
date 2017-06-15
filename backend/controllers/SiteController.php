<?php
namespace backend\controllers;

use common\models\AdminUser;
use Jasny\SSO\Broker;
use Jasny\SSO\Exception;
use Jasny\SSO\NotAttachedException;
use backend\logic\ThirdLogic;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $power = Yii::$app->params['power'];
        $serverUrl = $power['serverUrl'];
        $brokerId = $power['appid'];
        $brokerSecret = $power['appsecret'];
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Yii::$app->homeUrl);
        }
        $broker = new Broker($serverUrl, $brokerId, $brokerSecret);
        $broker->attach(true);
    
        try {
            $user = $broker->getUserInfo();
        
            if(!$user && empty($user))
            {
                $broker->clearToken();
                return $this->redirect($power['loginUrl']);
                //跳转到单点登录页面http://ssourl/login.php?return_url=xxxxx
                //return_url 可选，带上该参数后登陆成功后默认跳转到return_url
            }
            $session = Yii::$app->session;
            $userAdmin = AdminUser::findOne($user['id']);
            $intRoleId = intval(Yii::$app->request->get('role_id'));
            $arrRoleIds = explode(',', $userAdmin->role_ids);
    
            if ($intRoleId && in_array($intRoleId, $arrRoleIds)) {
                $session->set('ROLE_ID', $intRoleId);
            } else {
                $session->set('ROLE_ID', $arrRoleIds[0]);
            }
            Yii::$app->user->login($userAdmin, 3600*12);
            return $this->redirect(Yii::$app->homeUrl);
        } catch (NotAttachedException $e) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } catch (Exception $e) {
            $errmsg = $e->getMessage();
            //跳转到单点登录页面http://ssourl/login.php?sso_error=$errormsg
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->destroy();
        return $this->redirect(Yii::$app->params['power']['loginUrl'].'/logout.php');
    }
    
    /**
     * @return array
     */
    public function actionNotify()
    {
        $strType = Yii::$app->request->get('api');//测试可以手动拉取
        if(empty($strType))
        {
            $strType = Yii::$app->request->post('api');//请求接口的时候告知变动类型是什么
        }
    
        $power = ThirdLogic::instance();
        switch($strType)
        {
            case 'projects/users':
                $power->getAdminUser();
                break;
            case 'projects/roles'://项目角色
                $power->getRoles();
                break;
            case 'projects/permission-tree'://项目菜单权限树形结构
                $power->getPermissionTree();//项目菜单
                break;
            //菜单信息变动
        }
    
        return ['message' => '更新成功'];
    }
}
