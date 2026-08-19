# User Instruction Memory

This file records user instructions, preferences, and teachings for reference in future interactions.

## Format

### User Instruction Entry
User instruction entries should follow this format:

[User Instruction Summary]
- Date: [YYYY-MM-DD]
- Context: [Mentioned scenario or time]
- Instructions:
  - [Content of user teaching or instruction, described line by line]

### Project Knowledge Entry
Entries discovered by the Agent during task execution should follow this format:

[Project Knowledge Summary]
- Date: [YYYY-MM-DD]
- Context: Discovered by Agent while performing [specific task description]
- Category: [Operations & Deployment|Build Methods|Testing Methods|Troubleshooting & Debugging|Workflow & Collaboration|Environment Configuration]
- Instructions:
  - [Specific knowledge points, described line by line]

## Deduplication Strategy
- Before adding a new entry, check for similar or identical instructions.
- If a duplicate is found, skip the new entry or merge it with the existing one.
- When merging, update the context or date information.
- This helps avoid redundant entries and keeps the memory file tidy.

## Entries

[Project Knowledge Summary]
- Date: 2026-08-19
- Context: Discovered by Agent while auditing code style of /workspace/moments and /workspace/moments-thinkphp
- Category: Environment Configuration
- Instructions:
  - 当前 devbox 环境没有 php-cli，apt 仓库也无法安装 php，`php -l` 不可用。
  - 校验 PHP 语法应改用 tree-sitter-php（`pip install tree-sitter tree-sitter-php`），用 `tree_sitter.Language(tree_sitter_php.language_php())` + Parser 解析，检查 `root_node.has_error`。
  - 两项目版权要求：统一为 sanqi，网址 https://xaacn.com；vendor 与第三方压缩库不修改。
  - moments/ 为原生 PHP（tab 缩进），moments-thinkphp/ 为 ThinkPHP 8（4 空格 PSR）。执行大改前先备份到 /tmp/opencode/。