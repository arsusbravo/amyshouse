<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function labelZh(): string
    {
        return match ($this) {
            self::Pending => '待確認',
            self::Confirmed => '已確認',
            self::Ready => '可取貨',
            self::Completed => '已完成',
            self::Cancelled => '已取消',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Ready => 'Ready for Pickup',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
