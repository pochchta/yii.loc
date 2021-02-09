<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    'mutexTimeout' => 1,                // seconds

    'pjaxTimeout' => 5000,              // milliSeconds

    'delayDateInput' => 1000,           // ms, задержка перед отправкой фильтра input type = date

    'maxLengthSearchParam' => 20,       // максимальная длина параметра поиска

    'delayAutoComplete' => 500,
    'minSymbolsAutoComplete' => 3,
    'maxLinesAutoComplete' => 10,       // кол-во строк в autoComplete

    'maxLinesView' => 100,              // кол-во строк в списках типа dropDown
    'maxLinesPrint' => 500,             // кол-во строк при выводе на печать
];
