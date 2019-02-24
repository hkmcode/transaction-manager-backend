<?php

return [
  'gcm' => [
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => 'My_ApiKey',
  ],
  'fcm' => [
        'priority' => 'high',
        'dry_run' => false,
        'apiKey' => 'AAAAFanBGng:APA91bFnmCx9RlQZw8aa0tz8RL2xp5WzcWvUVfCloQacL20QiEfNhzWzi7MdXtTiDsuoA3zH6GWzbiRD-OD291RSi7ZkBYRxzOg6oMUdsPzGS1z6lwYKVtDydjAI3_R0OZk9fXkNK72M',
  ],
  'apn' => [
      'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
      'passPhrase' => '1234', //Optional
      'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
      'dry_run' => true
  ]
];