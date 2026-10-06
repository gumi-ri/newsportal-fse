# NewsPortal FSE

百度新闻（news.baidu.com）布局风格的 WordPress 区块主题（全站编辑 FSE 主题）。

## 主要特性

- **顶部导航**：顶栏左侧标语 + 右侧分类导航（产业资讯 / 湘潭汽车产业集群 / 96871(湘潭) / 关于莲企通）+ 站点 Logo + 搜索框（胶囊样式）+「看视频」按钮
- **移动端适配**：≤782px 响应式布局，搜索栏 grid 两行排列，主体内容 12px 边距
- **首屏双栏**：
  - 左栏 390px：「最新」热点列表（全站最新 6 篇）+ 焦点列表（offset 6 续接第 7-14 篇）+ 推荐 feed
  - 右栏 560px：大图轮播（筛「图文」标签）+ 热搜新闻词
- **分类板块**：产业资讯 / 湘潭汽车产业集群，各含焦点报道 + 新闻图片 + 新闻资讯三列
- **文章页**：640px 阅读流 + 304px 侧栏（相关文章 + 评论区），无作者，来源行动态提取正文「来源：XXX」
- **互动**：点赞计数 + 分享复制链接
- **评论**：仅需姓名，无需邮箱/网站
- **404 页**：热搜词引导
- **响应式**：≤1040px 双栏堆叠

## 安装步骤

1. 后台「外观 → 主题 → 上传主题」选择 `newsportal-fse.zip` 安装并启用
2. 「设置 → 固定链接」选择「文章名」或自定义结构
3. 后台「外观 → 自定义 → 站点身份」上传站点 Logo
4. 按需在「外观 → 编辑」中修改模板的板块筛选条件

## 部署

1. 打包：`tar -czf newsportal-fse.tar.gz -C /path/to/newsportal-fse .`
2. 上传压缩包到服务器 `wp-content/themes/` 目录并解压，或通过 WordPress 后台安装
3. 激活主题
4. 更新 `style.css` 后须在 `functions.php` 中递增版本号以破缓存

## 系统要求

- WordPress ≥ 6.4
- PHP ≥ 7.2

## 自定义常用入口

| 项目 | 操作路径 |
|---|---|
| 站点 Logo | 后台「外观 → 自定义 → 站点身份」 |
| 搜索按钮文字 | 编辑 `parts/header.html` 中 `wp:search` |
| 热搜词条 | 编辑 `front-page.html` / `404.html` 中热搜瓷砖 |
| 顶栏链接 | 编辑 `parts/header.html` 中 `np-topbar-links` |
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
│   ├── header.html      头部（Logo + 品牌副标题 + 搜索栏 + 导航）
│   ├── footer.html      页脚
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