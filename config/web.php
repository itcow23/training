<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timezone' => 'Asia/Ho_Chi_Minh',
    'container' => [
        'singletons' => [
            \yii\mail\MailerInterface::class => [
                'class' => \yii\symfonymailer\Mailer::class,
                // send all mails to a file by default.
                'useFileTransport' => true,
                'viewPath' => '@app/mail',
            ],
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '1234567890abcdef1234567890abcdef',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->format === \yii\web\Response::FORMAT_JSON && $response->statusCode !== 204) {
                    $isSuccess = $response->isSuccessful;

                    if (is_array($response->data) && isset($response->data['success'])) {
                        return;
                    }

                    $actionName = '';
                    if (\Yii::$app->controller && \Yii::$app->controller->action) {
                        $actionName = ucwords(str_replace('-', ' ', \Yii::$app->controller->action->id));
                    }

                    $message = $isSuccess ? ($actionName ? "$actionName success" : 'Success') : ($response->statusText ?: 'Error');
                    $errors = [];
                    $data = [];

                    if (!$isSuccess) {
                        if (is_string($response->data) && !empty($response->data)) {
                            $message = $response->data;
                        } elseif (is_array($response->data)) {
                            if (isset($response->data['message'])) {
                                $message = $response->data['message'];
                            }
                            if (isset($response->data['errors'])) {
                                $errors = $response->data['errors'];
                            } elseif (!isset($response->data['status']) || !is_int($response->data['status'])) {
                                $errors = $response->data;
                                $message = 'Validation failed.';
                            }
                        }
                    } else {
                        $data = $response->data;
                        $headers = $response->headers;
                        if ($headers->has('X-Pagination-Total-Count')) {
                            $data = [
                                'items' => $response->data,
                                'pagination' => [
                                    'totalCount' => (int)$headers->get('X-Pagination-Total-Count'),
                                    'pageCount' => (int)$headers->get('X-Pagination-Page-Count'),
                                    'currentPage' => (int)$headers->get('X-Pagination-Current-Page'),
                                    'perPage' => (int)$headers->get('X-Pagination-Per-Page'),
                                ],
                            ];
                        } else {
                            $data = $response->data;
                        }
                    }

                    $response->data = [
                        'success' => $isSuccess,
                        'message' => $message,
                        $isSuccess ? 'data' : 'errors' => $isSuccess ? $data : $errors,
                    ];
                }
            },
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => \yii\mail\MailerInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => true,
            'rules' => [
                // '<controller>' => '<controller>/index',

                // '<controller>/create' => '<controller>/create',

                // '<controller>/<id:\d+>' => '<controller>/view',

                // '<controller>/update/<id:\d+>' => '<controller>/update',

                // '<controller>/delete/<id:\d+>' => '<controller>/delete',

                // 'debug/<controller>/<action>' => 'debug/<controller>/<action>',
            ],
        ],

        'media' => [
            'class' => 'app\components\MediaComponent',
            'basePath' => '@webroot/uploads/',
            'baseUrl' => '/uploads',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
