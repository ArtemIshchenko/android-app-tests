<?php

return [
    'GET /'=>'v1/site/index',
	'GET api/v1.0/site'=>'v1/site/index',

    //Гороскопы
	'GET api/v1.0/facebook'=>'v1/facebook/index',
	'POST api/v1.0/facebook'=>'v1/facebook/index',

    'GET api/v1.0/facebook-lead'=>'v1/facebook-lead/index',
    'POST api/v1.0/facebook-lead'=>'v1/facebook-lead/index',

    'GET api/v1.0/unisender'=>'v1/uni-sender/index',
    'POST api/v1.0/unisender'=>'v1/uni-sender/index',

    'GET api/v1.0/viber'=>'v1/viber/index',
    'POST api/v1.0/viber'=>'v1/viber/index',

    'GET api/v1.0/telegram'=>'v1/telegram/index',
    'POST api/v1.0/telegram'=>'v1/telegram/index',

    'GET api/v1.0/ok'=>'v1/ok/index',
    'POST api/v1.0/ok'=>'v1/ok/index',

    'GET api/v1.0/vk'=>'v1/vk/index',
    'POST api/v1.0/vk'=>'v1/vk/index',

    'GET api/v1.0/advice'=>'v1/advice/index',
    'POST api/v1.0/advice'=>'v1/advice/index',

    //Обновление данных юзера по последней активнсоти и таймзоне
    'POST api/v1.0/user-update'=>'v1/statistic/user-update',


    //Test
    'GET api/v1.0/telegram-test'=>'v1/telegram-test/index',
    'POST api/v1.0/telegram-test'=>'v1/telegram-test/index',

    'GET api/v1.0/stat'=>'v1/statistic/index',
    'POST api/v1.0/stat'=>'v1/statistic/index',

    'GET api/v1.0/taro'=>'v1/taro/index',
    'POST api/v1.0/taro'=>'v1/taro/index',

    'GET api/v1.0/stat-activation'=>'v1/statistic-activation/index',
    'POST api/v1.0/stat-activation'=>'v1/statistic-activation/index',

    'GET api/v1.0/mt-subscribe'=>'v1/statistic/mt-subscribe',
    'POST api/v1.0/mt-subscribe'=>'v1/statistic/mt-subscribe',



    //Сонники
    'GET api/v1.0/dream-telegram'=>'v1/dream-telegram/index',
    'POST api/v1.0/dream-telegram'=>'v1/dream-telegram/index',

    'GET api/v1.0/dream-viber'=>'v1/dream-viber/index',
    'POST api/v1.0/dream-viber'=>'v1/dream-viber/index',

    'GET api/v1.0/dream-facebook'=>'v1/dream-facebook/index',
    'POST api/v1.0/dream-facebook'=>'v1/dream-facebook/index',

    'GET api/v1.0/dream-stat'=>'v1/dream-statistic/index',
    'POST api/v1.0/dream-stat'=>'v1/dream-statistic/index',


    //Таро
    'GET api/v1.0/taro-telegram'=>'v1/taro-telegram/index',
    'POST api/v1.0/taro-telegram'=>'v1/taro-telegram/index',

    'GET api/v1.0/taro-viber'=>'v1/taro-viber/index',
    'POST api/v1.0/taro-viber'=>'v1/taro-viber/index',

    'GET api/v1.0/taro-facebook'=>'v1/taro-facebook/index',
    'POST api/v1.0/taro-facebook'=>'v1/taro-facebook/index',

    'GET api/v1.0/taro-stat'=>'v1/taro-statistic/index',
    'POST api/v1.0/taro-stat'=>'v1/taro-statistic/index',

    'GET api/v1.0/taro-scheduler-stat'=>'v1/taro-statistic/scheduler',
    'POST api/v1.0/taro-scheduler-stat'=>'v1/taro-statistic/scheduler',

    //Андроид
    'GET api/v1.0/and/user'=>'v1/android/user',
    'POST api/v1.0/and/user'=>'v1/android/user',

    'GET api/v1.0/and/statistic'=>'v1/android/statistic',
    'POST api/v1.0/and/statistic'=>'v1/android/statistic',

    'GET api/v1.0/and/coin-statistic'=>'v1/android/coin-statistic',
    'POST api/v1.0/and/coin-statistic'=>'v1/android/coin-statistic',

    'GET api/v1.0/and/goroskop-statistic'=>'v1/android/goroskop-statistic',
    'POST api/v1.0/and/goroskop-statistic'=>'v1/android/goroskop-statistic',

    'GET api/v1.0/and/advice-statistic'=>'v1/android/advice-statistic',
    'POST api/v1.0/and/advice-statistic'=>'v1/android/advice-statistic',

    'GET api/v1.0/and/feedback'=>'v1/android/feedback',
    'POST api/v1.0/and/feedback'=>'v1/android/feedback',

    'GET api/v1.0/and/taro'=> 'v1/android/taro',
    'POST api/v1.0/and/taro'=> 'v1/android/taro',

    'GET api/v1.0/and/advice'=> 'v1/android/advice',
    'POST api/v1.0/and/advice'=> 'v1/android/advice',

    'GET api/v1.0/and/advices'=> 'v1/android/advices',
    'POST api/v1.0/and/advices'=> 'v1/android/advices',

    'GET api/v1.0/and/read-advice'=> 'v1/android/read-advice',
    'POST api/v1.0/and/read-advice'=> 'v1/android/read-advice',

    'GET api/v1.0/and/read-goroskop'=> 'v1/android/read-goroskop',
    'POST api/v1.0/and/read-goroskop'=> 'v1/android/read-goroskop',

    'GET api/v1.0/and/app'=> 'v1/android/set-application',
    'POST api/v1.0/and/app'=> 'v1/android/set-application',

    'GET api/v1.0/and/user-share'=> 'v1/android/user-share',
    'POST api/v1.0/and/user-share'=> 'v1/android/user-share',

    'GET api/v1.0/and/dwn-goroskop'=> 'v1/android/download-goroskop',
    'POST api/v1.0/and/dwn-goroskop'=> 'v1/android/download-goroskop',

    'GET api/v1.0/and/notify'=> 'v1/android-notify/notify',
    'POST api/v1.0/and/notify'=> 'v1/android-notify/notify',

    'GET api/v1.0/and/notify-statistic'=> 'v1/android-notify/statistic',
    'POST api/v1.0/and/notify-statistic'=> 'v1/android-notify/statistic',

    'GET api/v1.0/and/crystal-email'=> 'v1/android-crystal/email',
    'POST api/v1.0/and/crystal-email'=> 'v1/android-crystal/email',

    'GET api/v1.0/and/crystal-pincode-recovery'=> 'v1/android-crystal/pincode-recovery',
    'POST api/v1.0/and/crystal-pincode-recovery'=> 'v1/android-crystal/pincode-recovery',

    'GET api/v1.0/and/crystal-lootboxes'=> 'v1/android-crystal/lootboxes',
    'POST api/v1.0/and/crystal-lootboxes'=> 'v1/android-crystal/lootboxes',

    'GET api/v1.0/and/crystal-user-info'=> 'v1/android-crystal/user-info',
    'POST api/v1.0/and/crystal-user-info'=> 'v1/android-crystal/user-info',

    'GET api/v1.0/and/crystal-update-user-data'=> 'v1/android-crystal/update-user-data',
    'POST api/v1.0/and/crystal-update-user-data'=> 'v1/android-crystal/update-user-data',

    'GET api/v1.0/and/test'=> 'v1/android/test',

    'GET api/v1.0/and/onbording'=> 'v1/android/onbording',
    'POST api/v1.0/and/onbording'=> 'v1/android/onbording',

    'GET api/v1.0/and/pay-packages'=> 'v1/android-pay/packages',
    'POST api/v1.0/and/pay-packages'=> 'v1/android-pay/packages',

    'GET api/v1.0/and/crystal-buy'=> 'v1/android-pay/crystal-buy',
    'POST api/v1.0/and/crystal-buy'=> 'v1/android-pay/crystal-buy',

    'GET api/v1.0/and/package-buy'=> 'v1/android-pay/package-buy',
    'POST api/v1.0/and/package-buy'=> 'v1/android-pay/package-buy',

    'GET api/v1.0/and/package-subscribe'=> 'v1/android-pay/package-subscribe',
    'POST api/v1.0/and/package-subscribe'=> 'v1/android-pay/package-subscribe',

];

