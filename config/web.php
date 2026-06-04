<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timezone' => 'Asia/Ho_Chi_Minh',
    'container' => [
        'definitions' => [
            \yii\rest\Serializer::class => [
                'collectionEnvelope' => 'items',
            ],
        ],
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

                    if (is_array($response->data) && isset($response->data['success'])) {
                        return;
                    }

                    $isSuccess = $response->isSuccessful;

                    if ($isSuccess) {
                        $response->data = [
                            'success' => true,
                            'message' => 'Success',
                            'data' => $response->data,
                        ];
                    } else {

                        $rawErrors = $response->data;

                        $message = (is_array($rawErrors) && isset($rawErrors['message']) && is_string($rawErrors['message']))
                            ? $rawErrors['message']
                            : ($response->statusText ?: 'Error');

                        $formattedData = [
                            'success' => false,
                            'message' => $message,
                        ];

                        if ($response->statusCode === 422) {
                            if (!empty($rawErrors)) {
                                $formattedData['errors'] = $rawErrors;
                            }
                        } elseif ($response->statusCode >= 500 && defined('YII_DEBUG') && YII_DEBUG) {
                            if (!empty($rawErrors)) {
                                $formattedData['errors'] = $rawErrors;
                            }
                        }

                        $response->data = $formattedData;
                    }
                }
            },
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\Account::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['auth/login'],
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
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
        ],
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
