<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    // http cache
    'cacheTimeOfWord' => 12*30*24*60*60,    // seconds
    'cacheTimeOfWordVersion' => 60*60,      // seconds
    'cacheTimeOfParams' => 24*60*60,        // seconds

    // mutex
    'mutexTimeout' => 1,                // seconds

    // html
    'maxLengthTextField' => 40,         // максимальная длина текстовых полей
    'maxLengthSearchParam' => 40,       // максимальная длина параметра поиска

    // javascript
    'pjaxTimeout' => 5000,              // milliSeconds

    'delayAutoComplete' => 500,
    'minSymbolsAutoComplete' => 3,
    'maxLinesAutoComplete' => 10,       // кол-во строк в autoComplete
    'maxElementsTabMenu' => 1000,       // кол-во строк во вкладке меню фильтров

    'delayDateInput' => 1000,           // ms, задержка перед отправкой фильтра input type = date

    'maxLinesIndex' => 20,              // кол-во строк при выводе в index
    'maxLinesPrint' => 500,             // кол-во строк при выводе на печать
    'maxLines' => 500,                  // кол-во строк при выводе на печать
];