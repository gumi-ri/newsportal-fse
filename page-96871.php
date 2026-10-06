<?php
/**
 * Template Name: 96871 湘潭板块全景
 * 96871(湘潭) 板块 Patchwall 落地页（经典模板，page-{slug}.php 层级生效）。
 * 复用站点整体模板风格：引入 header/footer 模板部件，亮色布局，无背景氛围图。
 */

$np_sections = array(
	array('id' => 31, 'name' => '惠企政策', 'en' => 'ENTERPRISE POLICY', 'slug' => 'huiqi-zhengce', 'accent' => '#CC0000', 'desc' => '政策文件 · 申报指南 · 补贴奖励'),
	array('id' => 30, 'name' => '湘潭汽车产业集群', 'en' => 'AUTO CLUSTER', 'slug' => 'auto-cluster', 'accent' => '#254282', 'desc' => '产业集群 · 链上企业 · 动态追踪'),
	array('id' => 33, 'name' => '专家评论', 'en' => 'EXPERT COMMENTS', 'slug' => 'zhuanjia-pinglun', 'accent' => '#3064BB', 'desc' => '专家观点 · 深度评论'),
	array('id' => 34, 'name' => '专家供稿', 'en' => 'EXPERT CONTRIBUTIONS', 'slug' => 'zhuanjia-gonggao', 'accent' => '#17A2B7', 'desc' => '专家来稿 · 一线供稿'),
	array('id' => 21, 'name' => '在湘潭', 'en' => 'IN XIANGTAN', 'slug' => 'zaixiangtan', 'accent' => '#7295B7', 'desc' => '本地动态 · 城市观察'),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
<style>
/* 96871 板块全景页专属样式（亮色，贴合站点整体风格） */
.np96871 .pw-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.np96871 .pw-card{position:relative;display:flex;flex-direction:column;border:1px solid #dee2e6;background:#fff;overflow:hidden;transition:box-shadow .35s ease,transform .35s ease;opacity:0;transform:translateY(26px)}
.np96871 .pw-card.pw-in{opacity:1;transform:translateY(0)}
.np96871 .pw-card:hover{transform:translateY(-4px);box-shadow:0 14px 34px -14px rgba(37,66,130,.35)}
.np96871 .pw-card::before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:var(--pc,#CC0000)}
.np96871 .pw-card-img{display:block;position:relative;height:150px;overflow:hidden;border-bottom:1px solid #dee2e6}
.np96871 .pw-card-img img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease}
.np96871 .pw-card:hover .pw-card-img img{transform:scale(1.05)}
.np96871 .pw-card-body{padding:16px 18px 4px}
.np96871 .pw-card-top{display:flex;align-items:baseline;justify-content:space-between;gap:10px}
.np96871 .pw-card-name{margin:0;font-size:17px;font-weight:700;color:#254282}
.np96871 .pw-card-count{flex:none;font-size:12px;color:#666;border:1px solid #dee2e6;border-radius:999px;padding:2px 10px;white-space:nowrap}
.np96871 .pw-card-en{margin:6px 0 0;font-size:11px;letter-spacing:.3em;color:#999}
.np96871 .pw-card-desc{margin:10px 0 0;font-size:13px;color:#666}
</style>
<style>
.np96871 .pw-list{list-style:none;margin:12px 0 0;padding:0 18px 4px;flex:1}
.np96871 .pw-list li{display:flex;align-items:baseline;gap:10px;padding:8px 0;border-top:1px dashed #dee2e6;font-size:14px}
.np96871 .pw-list li:first-child{border-top:0}
.np96871 .pw-list a{flex:1;color:#222;line-height:1.6;overflow:hidden;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;transition:color .2s}
.np96871 .pw-list a:hover{color:#2e61bf;text-decoration:underline}
.np96871 .pw-list time{flex:none;font-size:12px;color:#999;font-variant-numeric:tabular-nums}
.np96871 .pw-card-foot{padding:12px 18px 16px}
.np96871 .pw-card-foot a{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--pc,#CC0000);transition:gap .25s}
.np96871 .pw-card-foot a:hover{gap:10px;text-decoration:underline}
.np96871 .pw-empty{padding:16px 18px 18px;font-size:13px;color:#999}
.np96871 .pw-foot{border-top:1px solid #dee2e6;margin-top:26px;padding:18px 0 30px;text-align:center;font-size:13px;color:#666}
@media (max-width:782px){
	.np96871 .pw-grid{grid-template-columns:1fr}
}
</style>
</head>
<body <?php body_class('np96871'); ?>>
<?php wp_body_open(); ?>
<?php block_template_part('header'); ?>

<main class="wp-block-group np-main">
	<section class="np-section" style="padding-top:16px">
		<div class="pw-grid">
			<?php foreach ($np_sections as $s) :
				$term  = get_term($s['id'], 'post_tag');
				$count = (is_wp_error($term) || ! $term) ? 0 : (int) $term->count;
				$q = new WP_Query(array('tag_id' => $s['id'], 'posts_per_page' => 4, 'ignore_sticky_posts' => true, 'no_found_rows' => true));
			?>
			<article class="pw-card" style="--pc:<?php echo esc_attr($s['accent']); ?>" data-reveal>
				<?php if ($q->have_posts()) : $np_first = $q->posts[0]; if (has_post_thumbnail($np_first->ID)) : ?>
				<a class="pw-card-img" href="<?php echo esc_url(get_permalink($np_first->ID)); ?>"><?php echo get_the_post_thumbnail($np_first->ID, 'medium_large'); ?></a>
				<?php endif; endif; ?>
				<div class="pw-card-body">
					<div class="pw-card-top">
						<h3 class="pw-card-name"><?php echo esc_html($s['name']); ?></h3>
						<span class="pw-card-count"><?php echo $count; ?> 篇</span>
					</div>
					<p class="pw-card-en"><?php echo esc_html($s['en']); ?></p>
					<p class="pw-card-desc"><?php echo esc_html($s['desc']); ?></p>
				</div>
				<?php if ($q->have_posts()) : ?>
				<ul class="pw-list">
					<?php while ($q->have_posts()) : $q->the_post(); ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><time><?php echo esc_html(get_the_date('m-d')); ?></time></li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
				<?php else : ?>
				<p class="pw-empty">板块筹备中，敬请期待。</p>
				<?php endif; ?>
				<div class="pw-card-foot"><a href="<?php echo esc_url('/tag/' . $s['slug'] . '/'); ?>">进入板块 →</a></div>
			</article>
			<?php endforeach; ?>
		</div>
	</section>
</main>

<?php block_template_part('footer'); ?>
<script>
(function () {
	var io = new IntersectionObserver(function (es) {
		es.forEach(function (e) {
			if (e.isIntersecting) { e.target.classList.add('pw-in'); io.unobserve(e.target); }
		});
	}, { threshold: 0.1 });
	document.querySelectorAll('[data-reveal]').forEach(function (el, i) {
		el.style.transitionDelay = (i % 3) * 100 + 'ms';
		io.observe(el);
	});
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
