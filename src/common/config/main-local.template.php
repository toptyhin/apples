<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=${DB_HOST};dbname=${DB_NAME}',
            'username' => '${DB_USER}',
            'password' => '${DB_PASSWORD}',
            'charset' => 'utf8mb4',
            // 'schemaCacheDuration' => 60,
            // 'schemaCache' => 'cache',
        ],
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];