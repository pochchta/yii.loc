<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    'mutexTimeout' => 1,                // seconds

    'pjaxTimeout' => 5000,              // milliSeconds

    'delayDateInput' => 1000,           // ms, задержка перед отправкой фильтра input type = date

    'maxLengthTextField' => 40,         // максимальная длина текстовых полей
    'maxLengthSearchParam' => 40,       // максимальная длина параметра поиска

    'delayAutoComplete' => 500,
    'minSymbolsAutoComplete' => 3,
    'maxLinesAutoComplete' => 10,       // кол-во строк в autoComplete
    'maxElementsTabMenu' => 1000,       // кол-во строк во вкладке меню фильтров

    'maxLinesIndex' => 20,              // кол-во строк при выводе в index
    'maxLinesPrint' => 500,             // кол-во строк при выводе на печать
];