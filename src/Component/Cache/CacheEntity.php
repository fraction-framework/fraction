<?php

namespace Fraction\Component\Cache;

enum CacheEntity: string {
  case GLOBAL = 'global';
  case CONTROLLER = 'controller';
  case CONFIG = 'config';
  case BINDER = 'binder';
  case EVENT_SUBSCRIBER = 'eventSubscriber';
}
