<?php

namespace Example\Todo;

enum Status: string
{
    case Todo = 'TODO';
    case Done = 'DONE';
}