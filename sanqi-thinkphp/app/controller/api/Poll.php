<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\model\Poll as PollModel;
use app\model\PollVote;
use think\facade\Cache;

class Poll extends Base
{
    /**
     * 提交投票
     * POST /api/poll/vote
     */
    public function vote()
    {
        $pollId = $this->request->post('poll_id', 0);
        // 支持单选(单个值)和多选(数组)
        $optionIndex = $this->request->post('option_index', null);
        $optionIndices = $this->request->post('option_indices', null);

        if (!$pollId) {
            return $this->error('参数错误');
        }

        $poll = PollModel::find($pollId);
        if (!$poll) {
            return $this->error('投票不存在');
        }

        if ($poll->isExpired()) {
            return $this->error('投票已过期');
        }

        $options = $poll->options;
        $optionCount = count($options);

        // 统一处理为数组
        if ($optionIndices !== null) {
            if (is_array($optionIndices)) {
                $indices = array_map('intval', $optionIndices);
            } else {
                // URLSearchParams 将数组转为逗号分隔字符串
                $indices = array_map('intval', array_filter(explode(',', (string)$optionIndices), 'strlen'));
            }
        } elseif ($optionIndex !== null) {
            $indices = [(int)$optionIndex];
        } else {
            return $this->error('请选择选项');
        }

        // 验证选项索引
        foreach ($indices as $idx) {
            if ($idx < 0 || $idx >= $optionCount) {
                return $this->error('选项无效');
            }
        }

        // 单选模式只允许一个选项
        if ($poll->type == 1 && count($indices) > 1) {
            return $this->error('单选只能选择一个选项');
        }

        // 多选模式不允许为空
        if (empty($indices)) {
            return $this->error('请选择至少一个选项');
        }

        // 获取用户标识
        $user = $this->getUser();
        $userId = $user ? $user['username'] : 'vis#-[' . ($this->request->ip() ?? '127.0.0.1') . ']-#vis';

        // 检查是否已投票
        if ($poll->hasVoted($userId)) {
            return $this->error('您已经投过票了');
        }

        // 保存投票
        $now = date('Y-m-d H:i:s');
        foreach ($indices as $idx) {
            PollVote::create([
                'poll_id' => $pollId,
                'user_id' => $userId,
                'option_index' => $idx,
                'created_at' => $now,
            ]);
        }

        Cache::tag('article')->clear();

        return $this->success('投票成功', $this->buildResult($poll, $userId));
    }

    /**
     * 获取投票结果
     * GET /api/poll/result?poll_id=1
     */
    public function result()
    {
        $pollId = $this->request->get('poll_id', 0);

        if (!$pollId) {
            return $this->error('参数错误');
        }

        $poll = PollModel::find($pollId);
        if (!$poll) {
            return $this->error('投票不存在');
        }

        $user = $this->getUser();
        $userId = $user ? $user['username'] : 'vis#-[' . ($this->request->ip() ?? '127.0.0.1') . ']-#vis';

        return $this->success('ok', $this->buildResult($poll, $userId));
    }

    /**
     * 构建投票结果数据
     */
    private function buildResult(PollModel $poll, string $userId): array
    {
        $options = $poll->options;
        $counts = $poll->getVoteCounts();
        $totalVotes = array_sum($counts);
        $hasVoted = $poll->hasVoted($userId);
        $myVotes = [];

        if ($hasVoted) {
            $myVotes = PollVote::where('poll_id', $poll->id)
                ->where('user_id', $userId)
                ->column('option_index');
        }

        $items = [];
        foreach ($options as $i => $text) {
            $count = $counts[$i] ?? 0;
            $percent = $totalVotes > 0 ? round($count / $totalVotes * 100, 1) : 0;
            $items[] = [
                'index' => $i,
                'text' => $text,
                'count' => $count,
                'percent' => $percent,
                'selected' => in_array($i, $myVotes),
            ];
        }

        return [
            'id' => $poll->id,
            'question' => $poll->question,
            'type' => $poll->type,
            'expired' => $poll->isExpired(),
            'total_votes' => $totalVotes,
            'has_voted' => $hasVoted,
            'options' => $items,
        ];
    }
}
