<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\model;

use think\Model;

class Poll extends Model
{
    protected $name = 'polls';
    protected $autoWriteTimestamp = false;

    /**
     * 获取选项数组
     */
    public function getOptionsAttr($value): array
    {
        return json_decode($value, true) ?: [];
    }

    /**
     * 设置选项数组
     */
    public function setOptionsAttr($value): string
    {
        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    /**
     * 关联投票记录
     */
    public function votes()
    {
        return $this->hasMany(PollVote::class, 'poll_id');
    }

    /**
     * 是否已过期
     */
    public function isExpired(): bool
    {
        return !empty($this->expire_at) && strtotime($this->expire_at) < time();
    }

    /**
     * 获取各选项投票数
     */
    public function getVoteCounts(): array
    {
        $counts = [];
        $options = $this->options;
        foreach ($options as $i => $opt) {
            $counts[$i] = $this->votes()->where('option_index', $i)->count();
        }
        return $counts;
    }

    /**
     * 用户是否已投票
     */
    public function hasVoted(string $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->count() > 0;
    }
}
