<?php

namespace App\Enums;

enum GenerationStatus: string
{
    case Pending = 'pending';
    case Generating = 'generating';
    case Completed = 'completed';
    case Failed = 'failed';
}
