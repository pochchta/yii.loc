<?php

namespace app\models;

interface Status
{
    const ALL = -1;                     // для всех свойств

    const NOT_CATEGORY = 0;

    const NOT_DELETED = 0;
    const DELETED = 1;
}