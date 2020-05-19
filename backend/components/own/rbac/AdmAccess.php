<?php

namespace backend\components\own\rbac;


use backend\models\db\adm\Adm;
use librariesHelpers\helpers\ArrayUtils\ArrayUtils;
use librariesHelpers\helpers\Type\Type_Cast;
use librariesHelpers\helpers\Utf8\Utf8;
use Yii;

class AdmAccess
{

    protected static $_instance = null;

    protected $_allowController = 'site';

    public function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance) || !is_a(self::$_instance, __CLASS__)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public function menuAdm()
    {
        $navMenu = [];

        //--------------------------------------------------------------------------------------------------------------
        //Меню Тесты
        $dataMenu = [];
        if ($this->_checkAccess('test/index')) {
            array_push($dataMenu, ['label' => '<i class="fa fa-modx"></i> Диплики', 'url' => ['test/index']]);
        }
        if ($this->_checkAccess('test/tests')) {
            array_push($dataMenu, ['label' => '<i class="fa fa-list"></i> Тесты', 'url' => ['test/tests']]);
        }
        if ($this->_checkAccess('test/setting')) {
            array_push($dataMenu, ['label' => '<i class="fa fa-sliders"></i> Настройки', 'url' => ['test/setting']]);
        }
        if (is_array($dataMenu) && !empty($dataMenu)) {
            $navMenu = ArrayUtils::merge($navMenu, [['label' => '<i class="fa fa-list"></i> Тесты', 'url' => '#', 'items' => $dataMenu,]]);
        }
        //--------------------------------------------------------------------------------------------------------------
        //Меню Система
        $dataMenu = [];
        if ($this->_checkAccess('moder/index')) {
            array_push($dataMenu, ['label' => '<i class="fa fa-user-secret"></i> Администраторы', 'url' => ['moder/index']]);
        }

        if (is_array($dataMenu) && !empty($dataMenu)) {
            $systemLabel = "Система";
            $navMenu = ArrayUtils::merge($navMenu, [['label' => '<i class="fa fa-wrench"></i> ' . $systemLabel, 'url' => '#', 'items' => $dataMenu]]);
        }
        return $navMenu;
    }

    protected function _checkAccess($urlAccess = null)
    {
        $model = $this->getModel();
        if (is_null($model) || empty($model)) {
            return false;
        }
        if ($model->is_root) {
            return true;
        }
        $access = Adm::unserializeRules($model->rules);
        if (!is_null($urlAccess)) {
            if ((is_array($urlAccess) && empty($urlAccess)) && (!is_array($urlAccess) && empty($urlAccess))) {
                return false;
            }
            if (is_array($urlAccess) && !empty($urlAccess)) {
                foreach ($urlAccess as $url) {
                    $urlAces = explode("/", $url);
                    if (empty($urlAces[0]) || empty($urlAces[1])) {
                        return false;
                    }
                    if (isset($urlAccess[0])) {
                        $urlAccess[0] = str_replace("-", "", $urlAccess[0]);
                    }
                    if (isset($urlAccess[1])) {
                        $urlAccess[1] = str_replace("-", "", $urlAccess[1]);
                    }
                    $controllerAccess = mb_strtoupper($urlAces[0] . "controller_action" . $urlAces[1]);
                    if (is_array($access) && !empty($access)) {
                        $accessCheck = isset($access[$controllerAccess]) ? $access[$controllerAccess] : 0;
                        if (Type_Cast::toBool($accessCheck)) {
                            return true;
                        }
                    }
                }
                return false;
            }
            $urlAccess = explode("/", $urlAccess);
            if (empty($urlAccess[0]) || empty($urlAccess[1])) {
                return false;
            }
            $urlAccess[0] = str_replace("-", "", $urlAccess[0]);
            $action = str_replace("-", "", $urlAccess[1]);
            $controllerAccess = mb_strtoupper($urlAccess[0] . "controller_action" . $action);
            if (is_array($access) && !empty($access)) {
                $accessCheck = isset($access[$controllerAccess]) ? $access[$controllerAccess] : 0;
                return Type_Cast::toBool($accessCheck);
            }
        }
        $controller = Yii::$app->controller->id;
        $controller = str_replace("-", "", $controller);
        $action = Yii::$app->controller->action->id;
        $action = str_replace("-", "", $action);
        $controllerAccess = mb_strtoupper($controller . "controller_action" . $action);
        if (($controller == $this->_allowController)) {
            return true;
        }
        if (is_array($access) && !empty($access)) {
            $accessCheck = isset($access[$controllerAccess]) ? $access[$controllerAccess] : 0;
            return Type_Cast::toBool($accessCheck);
        }
        return false;
    }

    public function getModel()
    {
        return Yii::$app->user->identity;
    }

    /**
     * Формируем права доступа для пользователей
     * @return array
     */
    public function getAllowActions()
    {
	    $model = $this->getModel();
        if (!is_null($model) && !empty($model)) {
            if ($model->is_root) {
                return [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                ];
            }
            $access = Adm::unserializeRules($model->rules);
            $controller = Yii::$app->controller->id;
            $controller = str_replace("-", "", $controller);
            $action = Yii::$app->controller->action->id;
            $actionOriginal = $action;
            $action = str_replace("-", "", $action);
            $controllerAccess = mb_strtoupper($controller . "controller_action" . $action);
            if (is_array($access) && !empty($access)) {
                $accessCheck = isset($access[$controllerAccess]) ? $access[$controllerAccess] : 0;
                if (Type_Cast::toBool($accessCheck)) {
                    return [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                            'actions' => [$actionOriginal, 'logout'],
                        ],
                        [
                            'actions' => ['login', 'error', 'logout'],
                            'allow' => true,
                        ],
                    ];
                }
                if ($controller == $this->_allowController) {
                    return [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                            'controllers' => ['site'],
                            'actions' => ['index', 'logout'],
                        ],
                        [
                            'actions' => ['login', 'error'],
                            'allow' => true,
                        ],
                    ];
                }
            }
        }
        return [
            [
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['login', 'error'],
            ],
            [
                'actions' => ['login', 'error'],
                'allow' => true,
            ],
        ];
    }

    /**
     * Публичный доступ к проверке прав доступа
     * @param null $urlAccess
     * @return bool
     */
    public function checkAccess($urlAccess = null)
    {
        return $this->_checkAccess($urlAccess);
    }

    /**
     * Возвращает список контролеров
     * @return array
     */
    public function getControllerActions()
    {
        $dir = ROOT_PATH . '/controllers/';
        $fileList = scandir($dir);
        $controllerList = array();
        $accessList = array();
        if (is_array($fileList) && !empty($fileList)) {
            foreach ($fileList as $value) {
                if ($value == "." || $value == ".." || $value == ".svn" || $value == ".git") {
                    array_shift($fileList);
                } else {
                    preg_match('(([A-Za-z0-9]*)(\.php)+)', $value, $tmp);
                    if ($tmp[1] != "SiteController" && $tmp[1] != "BackController") {
                        $controllerList[] = $tmp[1];
                    }
                }
            }
            unset($value);
        }
        if (is_array($controllerList) && !empty($controllerList)) {
            foreach ($controllerList as $className) {
                $reflection = new \ReflectionClass('backend\controllers\\' . $className);
                $methods = $reflection->getMethods();
                $accessList[$className]["classDoc"][] = self::parseDocComment($reflection->getDocComment());
                $accessList[$className]["className"][] = $className;
                foreach ($methods as $method) {
                    if ($method->class == 'backend\controllers\\' . $className) {
                        if (preg_match('(action)', $method->name)) {
                            $accessList[$className]["method"][] = $method->name;
                            $accessList[$className]["doc"][] = self::parseDocComment($method->getDocComment());
                        }
                    }
                }
                unset($method);
            }
            unset($className);
        }
        return $accessList;
    }

    /**
     * Парсит php-доки для автоматического чтения прав доступа
     * @param $querty
     * @return string
     */
    public static function parseDocComment($querty)
    {
        $masQuerty = Utf8::explode("*", $querty);
        if (!empty($masQuerty) && !empty($masQuerty)) {
            foreach ($masQuerty as $value) {
                if (preg_match('/(property-description)(.*)/i', $value, $matches)) {
                    $querty = $matches[2];
                    break;
                } else {
                    $querty = "";
                }
            }
        }
        return $querty;
    }

}