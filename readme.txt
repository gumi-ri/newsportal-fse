=== NewsPortal FSE ===

百度新闻（news.baidu.com）布局风格的 WordPress 区块主题（全站编辑 FSE 主题），已适配部署于湘潭产业新闻站（https://www.xt96871.com/ 莲企通）：站点 Logo 头部 / 「最新」热点流（全站最新文章，大字体 6 条 + 小字体续列 8 条零重复，排除图文类）/ 大图轮播与板块新闻图片（筛「图文」标签）/ 热搜瓷砖 / 产业资讯 + 湘潭汽车产业集群两个分类板块（焦点+新闻图片+新闻资讯三列）。

== 主要特性 ==
* 顶部：顶栏左侧标语「湘潭产业资讯共享平台-湘潭96871企业服务中心出品」+ 右侧分类导航（产业资讯 / 湘潭汽车产业集群 / 96871(湘潭) / 关于莲企通→/2024/06/01/about/）+ 站点 Logo（wp:site-logo，后台「外观 → 自定义 → 站点身份」上传即替换）+ 搜索框（浅灰胶囊聚焦蓝晕，内嵌放大镜图标，按钮文字「搜索」）+「看视频」橙色渐变胶囊按钮（新窗口打开抖音主页）
* 搜索栏移动端（≤782px）：grid-template-areas 两行布局——第一行 logo（36px 高自适应收缩）+ 看视频按钮（36px）左右排列，第二行搜索框通栏；搜索栏与主体内容 .np-main 均加 12px 左右边距防贴边；PC 端三列 grid 布局不变
* 首屏左栏 390px：「最新」Tab + 红点 #DA4453 热点列表（首条 18px 粗体，全站最新 6 篇，全区块墨迹间距恒定 20px）+ 蓝点 #7295B7 焦点列表（无筛选 offset 6 续接第 7-14 篇，与大字体列表零重复）+ 推荐 feed（95×66 缩略图）；大小字列表均排除「图文」标签文章（pre_get_posts 按查询特征识别：非主查询 + 无 tax_query/搜索词 + 6条offset0 或 8条offset6，设 tag__not_in，图文内容仅供图片位）
* 首屏右栏 560px：大图轮播 560×305（底部遮罩标题，筛 tag「图文」带特色图文章）+ 热搜新闻词 HOT WORDS（蓝/浅蓝瓷砖网格，前两条大瓷砖，点击跳转站内搜索或分类）
* 板块新闻图片位：筛选「所属分类 AND tag『图文』」（taxQuery 同时传 category 与 post_tag）
* 坑：Query Loop 区块 taxQuery 的标签键必须写 "post_tag"（taxonomy 名），写 "tag" 或 "tags" 会被核心静默忽略（不报错、无筛选），此前首页标签筛选一直未生效即因此；分类键 "category" 本身即 taxonomy 名可直接用
* 2 个分类板块（通栏三列：本地焦点 / 新闻图片 240×160 / 新闻资讯），按标题/正文含「汽车」关键词拆分文章来源：
  - 产业资讯（id:23，slug: industry-news，不含「汽车」的文章）
  - 湘潭汽车产业集群（id:24，slug: xiangtan-auto，标题或正文含「汽车」的文章）
  - 96871(湘潭)（id:28，slug: 96871-xiangtan，暂无文章，仅在顶栏导航提供归档入口）
* 文章页（百家号风格）：正文 640px 阅读流（28px 标题 / 来源行「文章来源：XXX」蓝底标签 + 时间 + 分类 / 17px·1.8 行距，不显示作者）+ 右侧栏 304px 卡片（相关文章蓝点虚线列表 + 评论区圆角表单），侧栏随滚动吸顶（top:105px 避开吸顶搜索栏）
* 文章来源动态提取：shortcode [np_article_source] 用正则从正文解析「来源：XXX」（多个来源取最后一个转载来源，兼容全角/半角冒号与括号包裹），解析不到时回退「莲企通」
* 文章页互动：「点赞」REST 计数（POST /wp-json/newsportal/v1/like/<id>，存 post meta _np_likes，localStorage 防重复）、「分享」一键复制本文链接（剪贴板 API + execCommand 兜底）
* 评论表单：仅保留姓名（去掉邮箱、网站、Cookie 勾选框），comment_form_default_fields 过滤器实现，站点选项 require_name_email 已设为 0
* 页脚：版权所有 © 湖南芒果果科技服务有限公司 · 由莲芯AI驱动，使用Yuri框架
* 坑：模板 HTML 文件切勿带 UTF-8 BOM（EFBBBF）——FSE 会把 BOM 渲染为 body 内文本节点，导致页顶出现多余空白（首页曾因此比其它页多一段空白）；另 PowerShell 写文件默认带 BOM，改模板时注意
* 坑：块注释必须严格配平（`<!-- wp:group -->` 开闭数量相等、div 开闭相等）——多出一个 `<!-- /wp:group -->` 会破坏块解析器配对，使模板尾部区块（如 footer template-part）整体不渲染且原样输出注释（首页曾因此丢失页脚）；拼接/删改模板后务必校验配对计数
* 归档/搜索/博客列表页：蓝色竖条页头 + 卡片式信息流（160×100 圆角缩略图、悬停浮起）+ 胶囊分页 + 居中空态；搜索页底部附内联搜索框
* 404 页：描边大字 404 + 统一风格搜索框 + 热搜瓷砖引导（湘潭专题 / 产业链招商 / 两个分类直达 + 站内搜索词）
* 首页/文章页各板块均为普通「循环查询」区块（无锁定 pattern 壳）：站点编辑器中点选任一板块，右侧「设置 → 筛选条件」即可改分类/标签/作者/关键词/排序/每页条数
* 迁移到新站点时各板块绑定的分类/标签 ID 会不同：进入「外观 → 编辑」打开首页模板，逐板块在「筛选条件」中重新勾选即可（一次性操作）
* 附加样式经 enqueue_block_assets 同时注入编辑器画布，门户双栏/三列采用 CSS Grid（列宽 minmax(0, …) 弹性收缩，容器 width:100% + max-width），编辑器内所见即所得、窄画布不溢出
* 响应式：≤1040px 双栏自动堆叠、文章页侧栏随文流堆叠
* 不使用任何百度商标素材（站名、Logo、按钮文字均可自定义）

== 安装步骤 ==
1. 后台「外观 → 主题 → 上传主题」选择 newsportal-fse.zip 安装并启用
2. 「设置 → 固定链接」选择「文章名」或自定义结构
3. 后台「外观 → 自定义 → 站点身份」上传站点 Logo（头部显示 Logo 而非站名文字）
4. 按需在「外观 → 编辑」中修改 front-page 等模板的板块筛选条件

== Docker 部署（当前线上方式） ==
目标站 https://www.xt96871.com/ 运行于 SSH 服务器（eous@192.168.0.124）的 Docker 容器 wordpress-wordpress-1：

1. 本地打包（源码目录含 junction 映射到 wp-content/themes/newsportal-fse）：
   tar -czf newsportal-fse.tar.gz -C .. newsportal-fse
2. 上传并部署（辅助脚本 _deploy/sshrun.py，paramiko）：
   python _deploy/sshrun.py --put newsportal-fse.tar.gz /tmp/newsportal-fse.tar.gz
   python _deploy/sshrun.py "docker cp /tmp/newsportal-fse.tar.gz wordpress-wordpress-1:/tmp/ && docker exec wordpress-wordpress-1 sh -c 'rm -rf /var/www/html/wp-content/themes/newsportal-fse && mkdir -p /var/www/html/wp-content/themes/newsportal-fse && tar -xzf /tmp/newsportal-fse.tar.gz -C /var/www/html/wp-content/themes/ && chown -R www-data:www-data /var/www/html/wp-content/themes/newsportal-fse'"
3. 激活主题（wp-cli 容器，需传数据库环境变量）：
   docker run --rm --volumes-from wordpress-wordpress-1 --network container:wordpress-wordpress-1 -e WORDPRESS_DB_HOST=db -e WORDPRESS_DB_USER=wpuser -e WORDPRESS_DB_PASSWORD=wppass123 -e WORDPRESS_DB_NAME=wordpress wordpress:cli wp theme activate newsportal-fse --allow-root
4. 更新 style.css 后必须在 functions.php 中递增版本号（当前 2.0.0）以破缓存

== 系统要求 ==
* WordPress ≥ 6.4
* PHP ≥ 7.2

== 自定义常用入口 ==
* 站点 Logo：后台「外观 → 自定义 → 站点身份」上传/更换（尺寸 160px 宽自适应）
* 搜索按钮文字：编辑 parts/header.html 中 wp:search 按钮文字
* 热搜词条：编辑 front-page/404 模板中热搜瓷砖的文字与链接
* 顶栏链接：编辑 parts/header.html 中 np-topbar-links
* 各板块文章来源：站点编辑器点选板块 →「设置 → 筛选条件」（分类/标签/排序/条数）
* 颜色：编辑 theme.json 中的色板（palette）

== 文件结构 ==
newsportal-fse/
├── style.css          主题头
├── theme.json         全局样式（v2：色板/字号/布局/核心区块样式）
├── functions.php      图案分类、附加样式加载（版本号 2.0.0）
├── templates/         模板（front-page/home/archive/single/search/404）
├── parts/             模板部件（header 含站点 Logo/footer）
├── patterns/          区块图案（hot-news/feed/hot-words/carousel/local-news/related-posts，模板中已展开为普通区块）
├── assets/style.css   附加像素级样式（含站点 Logo 样式）
├── assets/app.js      文章页点赞/分享交互
├── _deploy/sshrun.py  SSH 部署辅助脚本（paramiko：执行命令/上传/下载）
└── screenshot.png     主题缩略图
