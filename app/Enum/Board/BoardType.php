<?php

declare(strict_types=1);

namespace App\Enum\Board;

enum BoardType: string
{
    case Notice = 'notice';

    case News = 'news';

    case PromotionalVideo = 'pr_video';

    case InfoOpen = 'info_open';


    public function boardName() : string
    {
        return match ($this) {
            self::Notice => '공지사항',
            self::News => '언론보도',
            self::PromotionalVideo => '홍보 동영상',
            self::InfoOpen => '정보공개'
        };
    }
}