<?php

namespace Chief\Stubs;

use Chief\Command;

class NonInterfaceImplementingCommand implements Command {
    public $handled = false;
}