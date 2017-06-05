1、在配置文件添加
```
'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
    ]
```

2、在passport\config\params-local.php里面添加配置
```
'projects'=>[
        'che.com' => [
            'allowIps' => ['*'],
            'tokenKey' => 'dRpg6fPFVyz6vVCC6gbCep3sOL-4qvtZ'
        ]
    ]
```