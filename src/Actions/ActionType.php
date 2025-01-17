<?php

namespace Vleap\Warps\Actions;

enum ActionType: string
{
    case Contract = 'contract';
    case Query = 'query';
    case Link = 'link';
}
