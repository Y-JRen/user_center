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

3、队列
```
'queue' => [
            'class' => \zhuravljov\yii\queue\redis\Queue::class,
            'redis' => 'redis', // connection ID
            'channel' => 'queue', // queue channel
        ],
```


4、支付宝移动支付配置

> 支付宝旧版移动支付需要私钥的路径

common\config_file\key