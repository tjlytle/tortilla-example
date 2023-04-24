<?php

namespace Example\Api;

enum Operation
{
    case CREATE;
    case READ;
    case UPDATE;
    case DELETE;
}