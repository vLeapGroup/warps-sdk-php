<?php

namespace Vleap\Actions;

enum ActionType: string
{
    case Contract = 'contract';
    case Query = 'query';
    case Link = 'link';
}
