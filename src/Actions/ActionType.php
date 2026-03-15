<?php

namespace Vleap\Warps\Actions;

enum ActionType: string
{
    case Transfer = 'transfer';
    case Contract = 'contract';
    case Query = 'query';
    case Collect = 'collect';
    case Compute = 'compute';
    case Link = 'link';
    case Prompt = 'prompt';
    case State = 'state';
    case Mount = 'mount';
    case Unmount = 'unmount';
}
