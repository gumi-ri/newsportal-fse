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
		'2.2.4'
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
 * 图文类文章只出现在右侧大图轮播与板块新闻图片位
 * 识别方式：无分类/标签筛选的最新文章块查询（大字 6 条 offset 0 / 小字 8 条 offset 6）
 */
function np_exclude_tuwen_from_tag_lists( $query ) {
	if ( is_admin() || $query->is_main_query() ) {
		return;
	}
	$ppp     = (int) $query->get( 'posts_per_page' );
	$offset  = (int) $query->get( 'offset' );
	$is_hot  = ( 6 === $ppp && 0 === $offset );
	$is_list = ( 8 === $ppp && 6 === $offset );
	if ( ! $is_hot && ! $is_list ) {
		return;
	}
	if ( ! empty( $query->get( 'tax_query' ) ) || ! empty( $query->get( 's' ) ) ) {
		return;
	}
	$query->set( 'tag__not_in', array( 27 ) );
}
add_action( 'pre_get_posts', 'np_exclude_tuwen_from_tag_lists' );

/**
 * 评论表单仅保留姓名（去掉邮箱、网站、Cookie 勾选框）
 */
function np_remove_comment_email_field( $fields ) {
	unset( $fields['email'], $fields['url'], $fields['cookies'] );
	return $fields;
}
add_filter( 'comment_form_default_fields', 'np_remove_comment_email_field' );

