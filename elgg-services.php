<?php

use ColdTrick\Newsletter\Di\Processor;
use ColdTrick\Newsletter\Di\Recipients;

return [
	Processor::name() => DI\autowire(Processor::class),
	Recipients::name() => DI\autowire(Recipients::class),
	
	// map classes to alias to allow autowiring
	Processor::class => DI\get(Processor::name()),
	Recipients::class => DI\get(Recipients::name()),
];
