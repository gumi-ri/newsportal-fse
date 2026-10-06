<?php
/**
 * NewsPortal FSE 主题功能
 *
 * 通用新闻网站模板：不预置任何品牌、地方信息或站点专属素材，
 * 站点名称、栏目、热搜词、广告素材均由使用方自行配置。
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
		'3.0.0'
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
 * 解析不到来源时回退为站点名称（不写死任何品牌）
 */
function np_article_source_shortcode() {
	$source = get_bloginfo( 'name' );
	if ( '' === $source ) {
		$source = __( '本站', 'newsportal-fse' );
	}
	$post = get_post();
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
 * 记录当前正在渲染的 core/query 区块的 className
 *
 * render_block_data 钩子在每个区块渲染前执行，把 core/query 区块的 className
 * 存进全局，供下面基于 className 的查询微调使用（避免依赖 perPage/offset 形状匹配）。
 * 依赖：模板中目标 query 区块的 attrs 带 className（front-page.html 已写入）。
 */
function np_track_query_block_class( $block ) {
	if ( isset( $block['blockName'] ) && 'core/query' === $block['blockName'] ) {
		$GLOBALS['np_current_query_class'] = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	}
	return $block;
}
add_filter( 'render_block_data', 'np_track_query_block_class' );

/**
 * 图片位 / 大图轮播只选带特色图片的文章
 *
 * className 命中 np-local-pic（板块「新闻图片」）或 np-carousel（右栏大图轮播）时，
 * 加 _thumbnail_id EXISTS：最新文章无图时自动顺延到下一篇有图文章，避免图片位留白。
 */
function np_pic_slot_require_thumbnail( $query ) {
	if ( is_admin() || $query->is_main_query() ) {
		return;
	}
	$current = isset( $GLOBALS['np_current_query_class'] ) ? $GLOBALS['np_current_query_class'] : '';
	if ( ! in_array( $current, array( 'np-local-pic', 'np-carousel' ), true ) ) {
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
 *   [np_ad slot="1"]                     —— 显示占位框（未绑定素材时）
 *   [np_ad slot="1" id="123"]            —— 绑定媒体库附件 ID
 *   [np_ad slot="1" img="https://..."]   —— 绑定指定图片 URL
 *   [np_ad slot="1" link="https://..."]  —— 绑定跳转链接
 *
 * 通用版：主题不预置任何图片、附件 ID 或外链，全部由使用方按需传入；
 * 未绑定素材时渲染可点击占位框，方便先搭好版式再补素材。
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

	$img_url = '';
	if ( '' !== $atts['img'] ) {
		$img_url = $atts['img'];
	} else {
		$id = '' !== $atts['id'] ? (int) $atts['id'] : 0;
		if ( $id > 0 && 'attachment' === get_post_type( $id ) ) {
			$full    = wp_get_attachment_image_url( $id, 'large' );
			$img_url = $full ? $full : wp_get_attachment_url( $id );
		}
	}

	$link     = esc_url( $atts['link'] );
	$link_att = '' !== $link ? ' href="' . $link . '" target="_blank" rel="noopener"' : '';

	if ( '' !== $img_url ) {
		return '<div class="wp-block-group np-ad-slot"><a class="np-ad-link"' . $link_att . '><img class="np-ad-img" src="' . esc_url( $img_url ) . '" alt="广告位' . esc_attr( $slot ) . '" loading="lazy"></a></div>';
	}

	return '<div class="wp-block-group np-ad-slot"><a class="np-ad-link np-ad-placeholder"' . $link_att . '>广告位 ' . esc_html( $slot ) . '（待上传素材）</a></div>';
}
add_shortcode( 'np_ad', 'np_ad_shortcode' );

/**
 * 页脚版权 shortcode [np_copyright]
 *
 * 动态输出「版权所有 © 年份 站点名称 · 保留所有权利」，
 * 年份取当前时间，站点名称取后台「设置 → 常规」，不含任何写死的品牌信息。
 */
function np_copyright_shortcode() {
	$year = date_i18n( 'Y' );
	$name = get_bloginfo( 'name' );
	$name = $name ? '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $name ) . '</a>' : '';

	return '<p class="np-copyright">' . sprintf(
		/* translators: 1: 当前年份，2: 站点名称 */
		esc_html__( '版权所有 © %1$s %2$s · 保留所有权利', 'newsportal-fse' ),
		esc_html( $year ),
		$name
	) . '</p>';
}
add_shortcode( 'np_copyright', 'np_copyright_shortcode' );
