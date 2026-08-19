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
use app\model\Message as MessageModel;

/**
 * 消息API控制器
 */
class Message extends Base
{
    /**
     * 消息操作
     */
    public function operate()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        // 要求登录
        if (!$this->requireLogin()) return;

        $user = $this->getUser();
        $id = max(0, intval($this->request->post('plid', 0)));
        $type = $this->request->post('type', '0');

        switch ($type) {
            case '0': // 标记已读
                if (empty($id)) {
                    return $this->error('参数错误');
                }
                MessageModel::where('id', $id)->where('suser', $user['username'])->update(['msg' => 1]);
                return $this->success('操作成功');

            case '-1': // 删除单条
                if (empty($id)) {
                    return $this->error('参数错误');
                }
                MessageModel::where('id', $id)->where('suser', $user['username'])->update(['msg' => -1]);
                return $this->success('操作成功');

            case '-2': // 删除所有
                MessageModel::where('suser', $user['username'])->update(['msg' => -1]);
                return $this->success('操作成功');

            case '-3': // 全部已读
                MessageModel::where('suser', $user['username'])->where('msg', 0)->update(['msg' => 1]);
                return $this->success('操作成功');

            default:
                return $this->error('参数错误');
        }
    }
}
