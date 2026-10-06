=== NewsPortal FSE ===

门户新闻风格的 WordPress 区块主题（全站编辑 FSE 主题），通用新闻网站模板：顶栏标语 + 自动读取站点分类的频道导航 + 站点标题 + 搜索框 / 左栏「热点要闻」信息流 + 右栏大图轮播与热搜词 / 通栏多列新闻板块。不绑定任何特定站点，不含品牌或地方信息。

== 主要特性 ==
* 顶部：顶栏左侧标语 + 右侧频道导航（读取站点分类，自动同步）+ 站点标题（wp:site-title，后台「设置 → 常规」改站点名即替换）+ 搜索框（浅灰胶囊聚焦蓝晕，内嵌放大镜图标，按钮文字「搜索」）
* 搜索栏移动端（≤782px）：站点标题与搜索框上下两行排列；搜索栏与主体内容 .np-main 均加 12px 左右边距防贴边；PC 端两列 grid 布局
* 首屏左栏 390px：「热点要闻」Tab + 红点 #DA4453 列表（首条 18px 粗体，最新 6 篇，全区块墨迹间距恒定 20px）+ 蓝点 #7295B7 焦点列表（offset 6 续接第 7-14 篇，与大字体列表零重复，无需任何分类/标签）
* 首屏右栏 560px：大图轮播 560×305（只取带特色图片的文章，CSS 淡入淡出，标题悬浮于底部遮罩）+ 热搜新闻词 HOT WORDS（蓝/浅蓝瓷砖网格，前两条大瓷砖，点击跳转站内搜索）+ 广告位 [np_ad]
* 通栏新闻板块：三列（焦点资讯列表 / 更多资讯列表 / 新闻图片大图），按 offset 取文，不依赖预建分类；「新闻图片」位只选带特色图片的文章（className=np-local-pic）
* 文章页：正文 640px 阅读流（28px 标题 / 来源行「文章来源：XXX」蓝底标签 + 时间 / 17px·1.8 行距，不显示作者）+ 右侧栏 304px 卡片（相关文章蓝点虚线列表 + 评论区圆角表单），侧栏随滚动吸顶（top:105px 避开吸顶搜索栏）
* 文章来源动态提取：shortcode [np_article_source] 用正则从正文解析「来源：XXX」（多个来源取最后一个转载来源，兼容全角/半角冒号与括号包裹），解析不到时回退为站点名称
* 文章页互动：「点赞」REST 计数（POST /wp-json/newsportal/v1/like/<id>，存 post meta _np_likes，localStorage 防重复）、「分享」一键复制本文链接（剪贴板 API + execCommand 兜底）
* 评论表单：仅保留姓名（去掉邮箱、网站、Cookie 勾选框），comment_form_default_fields 过滤器实现
* 页脚：shortcode [np_copyright] 动态输出「版权所有 © 年份 站点名称 · 保留所有权利」，不含任何品牌
* 广告位：shortcode [np_ad slot="1"]，默认渲染可点击占位框；用 id / img / link 属性绑定素材与跳转，主题不预置任何图片或外链
* 坑：模板 HTML 文件切勿带 UTF-8 BOM（EFBBBF）——FSE 会把 BOM 渲染为 body 内文本节点，导致页顶出现多余空白；用 PowerShell 写文件默认带 BOM，改模板时注意
* 坑：块注释必须严格配平（`<!-- wp:group -->` 开闭数量相等、div 开闭相等）——多出一个 `<!-- /wp:group -->` 会破坏块解析器配对，使模板尾部区块（如 footer template-part）整体不渲染且原样输出注释；拼接/删改模板后务必校验配对计数
* 归档/搜索/博客列表页：蓝色竖条页头 + 卡片式信息流（160×100 圆角缩略图、悬停浮起）+ 胶囊分页 + 居中空态；搜索页底部附内联搜索框
* 404 页：描边大字 404 + 统一风格搜索框 + 热搜瓷砖引导
* 首页/文章页各板块均为普通「循环查询」区块（无锁定 pattern 壳）：站点编辑器中点选任一板块，右侧「设置 → 筛选条件」即可改分类/标签/作者/关键词/排序/每页条数
* 附加样式经 enqueue_block_assets 同时注入编辑器画布，门户双栏/三列采用 CSS Grid（列宽 minmax(0, …) 弹性收缩，容器 width:100% + max-width），编辑器内所见即所得、窄画布不溢出
* 响应式：≤1040px 双栏自动堆叠、文章页侧栏随文流堆叠
* 不使用任何第三方商标素材（站名、栏目、Logo、按钮文字均可自定义）

== 安装步骤 ==
1. 后台「外观 → 主题 → 上传主题」选择 newsportal-fse.zip 安装并启用
2. 「设置 → 固定链接」选择「文章名」或自定义结构
3. 「设置 → 常规」填写站点标题与副标题（头部自动显示站点标题）
4. 在「文章 → 分类」中建立自己的频道，顶栏导航会自动列出
5. 按需在「外观 → 编辑」中调整 front-page 等模板的板块筛选条件

== 部署 ==
1. 打包：`tar -czf newsportal-fse.tar.gz -C /path/to/newsportal-fse .`
2. 上传压缩包到服务器 wp-content/themes/ 目录并解压，或通过 WordPress 后台「外观 → 主题 → 上传」安装
3. 激活主题
4. 更新 assets/style.css 后必须在 functions.php 中递增版本号以破缓存

== 系统要求 ==
* WordPress ≥ 6.4
* PHP ≥ 7.2

== 自定义常用入口 ==
* 站点标题：后台「设置 → 常规 → 站点标题」
* 顶栏频道导航：后台「文章 → 分类」增删分类（顶栏自动同步）
* 顶栏标语：编辑 parts/header.html 中 np-topbar-slogan
* 搜索按钮文字：编辑 parts/header.html 中 wp:search 按钮文字
* 热搜词条：编辑 parts/hot-words.html 中热搜瓷砖的文字与链接
* 页脚版权：编辑 functions.php 中 np_copyright 短代码
* 广告位素材：在编辑器给 [np_ad] 短代码加 id / img / link 属性
* 各板块文章来源：站点编辑器点选板块 →「设置 → 筛选条件」（分类/标签/排序/条数）
* 颜色：编辑 theme.json 中的色板（palette）

== 文件结构 ==
newsportal-fse/
├── style.css          主题头
├── theme.json         全局样式（v2：色板/字号/布局/核心区块样式）
├── functions.php      图案分类、附加样式加载、短代码（点赞/来源/广告/版权）
├── templates/         模板（front-page/home/archive/single/search/404）
├── parts/             模板部件（header 站点标题与频道导航 / footer 动态版权）
├── patterns/          区块图案（hot-news/feed/hot-words/carousel/local-news/related-posts，模板中已展开为普通区块）
├── assets/style.css   附加像素级样式
├── assets/app.js      文章页点赞/分享交互
└── screenshot.png     主题缩略图
