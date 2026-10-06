<?php
/**
 * NewsPortal FSE 主题功能
 *
 * @package newsportal-fse
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 注册「门户板块」图案分类
 */
function newsportal_register_pattern_category() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'newsportal',
			array( 'label' => __( '门户板块', 'newsportal-fse' ) )
		);
	}
}
add_action( 'init', 'newsportal_register_pattern_category' );

/**
 * 加载附加样式（前台与区块编辑器共用，保证所见即所得）
 * wp_enqueue_scripts：前台输出（压轴覆盖核心样式）
 * enqueue_block_assets：站点/区块编辑器画布内也加载，防止编辑器渲染崩坏
 */
function newsportal_enqueue_assets() {
	wp_enqueue_style(
		'newsportal-extra',
		get_template_directory_uri() . '/assets/style.css',
		array(),
		'2.5.7'
	);
}
add_action( 'wp_enqueue_scripts', 'newsportal_enqueue_assets', PHP_INT_MAX );
add_action( 'enqueue_block_assets', 'newsportal_enqueue_assets' );

/**
 * 点赞 REST 端点：POST /wp-json/newsportal/v1/like/<post_id>
 * 计数存入 post meta `_np_likes`
 */
function newsportal_register_like_route() {
	register_rest_route(
		'newsportal/v1',
		'/like/(?P<post_id>\d+)',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'callback'            => function ( WP_REST_Request $request ) {
				$post_id = (int) $request['post_id'];
				if ( $post_id < 1 || 'post' !== get_post_type( $post_id ) ) {
					return new WP_Error( 'newsportal_invalid_post', __( '文章不存在', 'newsportal-fse' ), array( 'status' => 404 ) );
				}
				$likes = (int) get_post_meta( $post_id, '_np_likes', true );
				update_post_meta( $post_id, '_np_likes', $likes + 1 );
				return array( 'likes' => $likes + 1 );
			},
		)
	);
}
add_action( 'rest_api_init', 'newsportal_register_like_route' );

/**
 * 文章页交互脚本（点赞 / 分享），传入 REST 地址与初始点赞数
 */
function newsportal_enqueue_app() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$post_id = get_the_ID();
	wp_enqueue_script(
		'newsportal-app',
		get_template_directory_uri() . '/assets/app.js',
		array(),
		'1.0.2',
		true
	);
	wp_localize_script(
		'newsportal-app',
		'npData',
		array(
			'restUrl' => esc_url_raw( rest_url( 'newsportal/v1/' ) ),
			'postId'  => (int) $post_id,
			'likes'   => (int) get_post_meta( $post_id, '_np_likes', true ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'newsportal_enqueue_app' );

/**
 * 文章来源 shortcode：从正文提取「来源：XXX」（存在多个时取最后一个转载来源）
 * 无来源信息时回退显示「莲企通」
 */
function np_article_source_shortcode() {
	$source = '莲企通';
	$post   = get_post();
	if ( $post ) {
		$content = wp_strip_all_tags( $post->post_content );
		if ( preg_match_all( '/来源[：:]\s*([^\s\x{3000}。！？；，<>()（）"\'’”]{2,40})/u', $content, $m ) ) {
			$source = trim( end( $m[1] ) );
		}
	}
	return '<span>' . esc_html( $source ) . '</span>';
}
add_shortcode( 'np_article_source', 'np_article_source_shortcode' );

/**
 * 首页「最新」大小字列表排除「图文」标签文章
 * 图文类文章只出现在轮播与板块「新闻图片」位，不应混进首页大小字最新列表。
 *
 * 精准识别（不再靠 perPage/offset 形状匹配，避免新增同形状查询被误命中）：
 *   1) render_block_data 钩子在每个区块渲染前，记录当前 core/query 区块的 className；
 *   2) 该区块的 WP_Query 触发 pre_get_posts 时，若 className 命中两个「最新」列表
 *      （np-hotnews / np-focuslist）就设 tag__not_in=[27]。
 *   其它任何 Query Loop（即使同样 6/0 或 8/6）className 不同，不会被误命中。
 * 依赖：front-page.html 中这两个 query 区块的 attrs 带 className（已写入）。
 */
function np_track_query_block_class( $block ) {
	if ( isset( $block['blockName'] ) && 'core/query' === $block['blockName'] ) {
		$GLOBALS['np_current_query_class'] = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	}
	return $block;
}
add_filter( 'render_block_data', 'np_track_query_block_class' );

function np_exclude_tuwen_from_tag_lists( $query ) {
	if ( is_admin() || $query->is_main_query() ) {
		return;
	}
	$targets = array( 'np-hotnews', 'np-focuslist' );
	$current = isset( $GLOBALS['np_current_query_class'] ) ? $GLOBALS['np_current_query_class'] : '';
	if ( ! in_array( $current, $targets, true ) ) {
		return;
	}
	$query->set( 'tag__not_in', array( 27 ) );
}
add_action( 'pre_get_posts', 'np_exclude_tuwen_from_tag_lists' );

/**
 * 板块「新闻图片」位只选带特色图片的文章
 * 两个板块图片查询（className=np-local-pic）加 _thumbnail_id EXISTS：
 * 最新文章无图时自动顺延到下一篇有图文章，避免图片位只剩标题条、大片留白。
 * 同样靠 render_block_data 记录的 className 精准命中，不影响其它查询。
 */
function np_pic_slot_require_thumbnail( $query ) {
	if ( is_admin() || $query->is_main_query() ) {
		return;
	}
	$current = isset( $GLOBALS['np_current_query_class'] ) ? $GLOBALS['np_current_query_class'] : '';
	if ( 'np-local-pic' !== $current ) {
		return;
	}
	$query->set(
		'meta_query',
		array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			),
		)
	);
}
add_action( 'pre_get_posts', 'np_pic_slot_require_thumbnail' );

/**
 * 评论表单仅保留姓名（去掉邮箱、网站、Cookie 勾选框）
 */
function np_remove_comment_email_field( $fields ) {
	unset( $fields['email'], $fields['url'], $fields['cookies'] );
	return $fields;
}
add_filter( 'comment_form_default_fields', 'np_remove_comment_email_field' );

/**
 * 广告位短代码 [np_ad slot="1"]
 *
 * 用法：
 *   [np_ad slot="1"]                     —— 显示该格固定绑定的图片，跳默认目标
 *   [np_ad slot="1" id="3209"]           —— 覆盖为指定媒体库附件 ID
 *   [np_ad slot="1" img="https://..."]   —— 覆盖为指定图片 URL
 *   [np_ad slot="1" link="https://..."]  —— 覆盖跳转链接
 *
 * 每格固定绑定一张图（不再随机）：slot→[附件ID, 跳转] 见下方 $ad_slots 表。
 * 当前绑定：slot1=96871直播中心亮色海报v2（3215，跳抖音）、slot2=湘潭工业信息大脑亮色海报v2（3216，跳关于页）。
 * 换图：在媒体库替换该附件文件（ID 不变）即生效；或删旧图后把新附件 ID 填进表里，
 * 或直接在编辑器给短代码加 id/img 属性。目标格无有效图时渲染可点击占位框。
 */
function np_ad_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'slot' => '1',
			'link' => '',
			'img'  => '',
			'id'   => '',
		),
		$atts,
		'np_ad'
	);

	$slot = preg_replace( '/\D/', '', $atts['slot'] );
	if ( '' === $slot ) {
		$slot = '1';
	}

	// slot => [ 固定附件 ID, 默认跳转 ]（2026-09-29：广告位减至 2 格；同日换亮色版-v2 海报 3215/3216，旧 3211/3212 留媒体库不引用）
	$ad_slots = array(
		'1' => array( 3215, 'https://www.douyin.com/user/MS4wLjABAAAAO7VLQtiptwu-L5mcSM6EcZ6A8-l5HjZgxUhAzHwSxqI' ),
		'2' => array( 3216, 'https://www.xt96871.com/2024/06/01/about/' ),
	);
	$conf = isset( $ad_slots[ $slot ] ) ? $ad_slots[ $slot ] : array( 0, 'https://www.xt96871.com/' );

	$link = '' !== $atts['link'] ? $atts['link'] : $conf[1];

	$img_url = '';
	if ( '' !== $atts['img'] ) {
		$img_url = $atts['img'];
	} else {
		$id = '' !== $atts['id'] ? (int) $atts['id'] : (int) $conf[0];
		if ( $id > 0 && 'attachment' === get_post_type( $id ) ) {
			$full    = wp_get_attachment_image_url( $id, 'large' );
			$img_url = $full ? $full : wp_get_attachment_url( $id );
		}
	}

	$link_esc = esc_url( $link );

	if ( '' !== $img_url ) {
		return '<div class="wp-block-group np-ad-slot"><a class="np-ad-link" href="' . $link_esc . '" target="_blank" rel="noopener"><img class="np-ad-img" src="' . esc_url( $img_url ) . '" alt="广告位' . esc_attr( $slot ) . '" loading="lazy"></a></div>';
	}

	return '<div class="wp-block-group np-ad-slot"><a class="np-ad-link np-ad-placeholder" href="' . $link_esc . '" target="_blank" rel="noopener">广告位 ' . esc_html( $slot ) . '（待上传素材）</a></div>';
}
add_shortcode( 'np_ad', 'np_ad_shortcode' );

