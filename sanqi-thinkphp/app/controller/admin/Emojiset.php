<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\model\Emoji;
use app\service\AdminLogService;

/**
 * 表情管理控制器
 */
class Emojiset extends \app\controller\Base
{
    /**
     * 表情列表
     */
    public function index()
    {
        $emojis = Emoji::order('sort_order', 'asc')->select()->toArray();
        $siteConfig = $this->getSiteConfig();

        return view('admin/emojis', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'emojis' => $emojis,
            'pageTitle' => '表情管理'
        ], $this->getAdminViewData()));
    }

    /**
     * 添加表情
     */
    public function add()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $name = trim($this->request->post('name', ''));
        $code = trim($this->request->post('code', ''));
        $filename = trim($this->request->post('filename', ''));
        $category = trim($this->request->post('category', 'paopao'));
        $sortOrder = intval($this->request->post('sort_order', 0));
        $status = intval($this->request->post('status', 1));

        if (empty($name) || empty($code) || empty($filename)) {
            return $this->error('名称、触发码和文件名不能为空');
        }

        // 检查触发码是否已存在
        $exists = Emoji::where('code', $code)->find();
        if ($exists) {
            return $this->error('触发码已存在');
        }

        $emoji = Emoji::create([
            'name'       => $name,
            'code'       => $code,
            'filename'   => $filename,
            'category'   => $category,
            'sort_order' => $sortOrder,
            'status'     => $status,
        ]);

        clearEmojiCache();
        AdminLogService::operation('emoji.add', 'emoji:' . $emoji->id, ['name' => $name, 'code' => $code]);

        return $this->success('添加成功');
    }

    /**
     * 更新表情
     */
    public function update()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $id = intval($this->request->post('id', 0));
        $name = trim($this->request->post('name', ''));
        $code = trim($this->request->post('code', ''));
        $filename = trim($this->request->post('filename', ''));
        $category = trim($this->request->post('category', 'paopao'));
        $sortOrder = intval($this->request->post('sort_order', 0));
        $status = intval($this->request->post('status', 1));

        if ($id <= 0 || empty($name) || empty($code) || empty($filename)) {
            return $this->error('参数错误');
        }

        // 检查触发码是否被其他记录使用
        $exists = Emoji::where('code', $code)->where('id', '<>', $id)->find();
        if ($exists) {
            return $this->error('触发码已被其他表情使用');
        }

        Emoji::where('id', $id)->update([
            'name'       => $name,
            'code'       => $code,
            'filename'   => $filename,
            'category'   => $category,
            'sort_order' => $sortOrder,
            'status'     => $status,
        ]);

        clearEmojiCache();
        AdminLogService::operation('emoji.update', 'emoji:' . $id, ['name' => $name, 'code' => $code]);

        return $this->success('更新成功');
    }

    /**
     * 删除表情
     */
    public function delete()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $id = intval($this->request->post('id', 0));
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $emoji = Emoji::find($id);
        if (!$emoji) {
            return $this->error('表情不存在');
        }

        Emoji::where('id', $id)->delete();
        clearEmojiCache();
        AdminLogService::operation('emoji.delete', 'emoji:' . $id, ['name' => $emoji->name]);

        return $this->success('删除成功');
    }

    /**
     * 切换表情启用/禁用状态
     */
    public function toggle()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $id = intval($this->request->post('id', 0));
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $emoji = Emoji::find($id);
        if (!$emoji) {
            return $this->error('表情不存在');
        }

        $newStatus = $emoji->status == 1 ? 0 : 1;
        Emoji::where('id', $id)->update(['status' => $newStatus]);

        clearEmojiCache();
        AdminLogService::operation('emoji.toggle', 'emoji:' . $id, ['status' => $newStatus]);

        return $this->success($newStatus == 1 ? '已启用' : '已禁用');
    }
}
