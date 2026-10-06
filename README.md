# NewsPortal FSE

门户新闻风格的 WordPress 区块主题（全站编辑 FSE 主题）——**通用新闻网站模板**。

不绑定任何特定站点，不含品牌或地方信息：站点名称、栏目、热搜词、配色均由使用方自行配置。

## 主要特性

- **顶部频道导航**：顶栏标语 + 自动读取站点分类的频道导航 + 站点标题 + 搜索框（胶囊样式）
- **移动端适配**：≤782px 响应式布局，搜索栏通栏排列，主体内容 12px 边距
- **首屏双栏**：
  - 左栏 390px：「热点要闻」列表（最新 6 篇）+ 焦点列表（offset 6 续接第 7-14 篇，与大字体列表零重复）
  - 右栏 560px：大图轮播（仅取带特色图片的文章）+ 热搜新闻词瓷砖 + 广告位
- **通栏新闻板块**：三列结构（焦点资讯列表 / 更多资讯列表 / 新闻图片大图），按偏移量取文，无需预先建立分类
- **文章页**：640px 阅读流 + 304px 侧栏（相关文章 + 评论区），无作者，来源行动态提取正文「来源：XXX」
- **互动**：点赞计数 + 分享复制链接
- **评论**：仅需姓名，无需邮箱/网站
- **404 页**：热搜词引导
- **响应式**：≤1040px 双栏堆叠

## 安装步骤

1. 后台「外观 → 主题 → 上传主题」选择 `newsportal-fse.zip` 安装并启用
2. 「设置 → 固定链接」选择「文章名」或自定义结构
3. 「设置 → 常规」填写站点标题（头部自动显示）
4. 在「文章 → 分类」中建立自己的频道，顶栏导航会自动列出
5. 按需在「外观 → 编辑」中调整各板块的筛选条件

## 部署

1. 打包：`tar -czf newsportal-fse.tar.gz -C /path/to/newsportal-fse .`
2. 上传压缩包到服务器 `wp-content/themes/` 目录并解压，或通过 WordPress 后台安装
3. 激活主题
4. 更新 `assets/style.css` 后须在 `functions.php` 中递增版本号以破缓存

## 系统要求

- WordPress ≥ 6.4
- PHP ≥ 7.2

## 自定义常用入口

| 项目 | 操作路径 |
|---|---|
| 站点标题 | 后台「设置 → 常规 → 站点标题」 |
| 顶栏频道导航 | 后台「文章 → 分类」增删分类（顶栏自动同步） |
| 顶栏标语 | 编辑 `parts/header.html` 中 `np-topbar-slogan` |
| 搜索按钮文字 | 编辑 `parts/header.html` 中 `wp:search` |
| 热搜词条 | 编辑 `parts/hot-words.html` 中热词瓷砖 |
| 页脚版权 | 编辑 `functions.php` 中 `np_copyright` 短代码（默认自动取年份与站点名） |
| 广告位素材 | 在编辑器给 `[np_ad slot="1"]` 短代码加 `id`/`img`/`link` 属性 |
| 各板块文章来源 | 站点编辑器点选板块 →「设置 → 筛选条件」 |
| 颜色 | 编辑 `theme.json` 中的色板 |

## 文件结构

```
newsportal-fse/
├── style.css            主题头
├── theme.json           全局样式配置
├── functions.php        功能入口与样式加载
├── README.md            本文件
├── readme.txt           WordPress 标准说明
├── screenshot.png       主题缩略图
├── assets/
│   ├── style.css        附加像素级样式
│   └── app.js           文章页点赞/分享交互
├── parts/
│   ├── header.html      头部（站点标题 + 搜索栏 + 频道导航）
│   ├── footer.html      页脚（动态版权）
│   └── hot-words.html   热搜词瓷砖（单源，首页/404 以 template-part 引用）
├── patterns/            区块图案（模板中已展开为普通区块）
└── templates/
    ├── front-page.html  首页
    ├── single.html      文章页
    ├── archive.html     归档/分类页
    ├── search.html      搜索页
    ├── 404.html         404 页
    ├── home.html        博客首页
    └── index.html       默认回退模板
```
