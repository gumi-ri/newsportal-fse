/**
 * NewsPortal FSE 前端交互：文章页 点赞 / 分享
 */
(function () {
	'use strict';

	var data = window.npData || {};
	var likedKey = 'npLiked_' + (data.postId || 0);

	function setCount(btn, n) {
		var span = btn.querySelector('.np-like-count');
		if (!span) {
			span = document.createElement('span');
			span.className = 'np-like-count';
			btn.appendChild(span);
		}
		span.textContent = n > 0 ? n : '';
	}

	/* 点赞：REST 记录到 post meta，localStorage 防重复 */
	var likeBtn = document.querySelector('.np-action-like');
	if (likeBtn && data.postId) {
		if (data.likes > 0) {
			setCount(likeBtn, data.likes);
		}
		try {
			if (localStorage.getItem(likedKey) === '1') {
				likeBtn.classList.add('np-liked');
			}
		} catch (e) {}

		likeBtn.addEventListener('click', function () {
			if (likeBtn.classList.contains('np-liked')) {
				return;
			}
			fetch(data.restUrl + 'like/' + data.postId, { method: 'POST' })
				.then(function (r) { return r.json(); })
				.then(function (res) {
					if (typeof res.likes === 'number') {
						setCount(likeBtn, res.likes);
						likeBtn.classList.add('np-liked');
						try {
							localStorage.setItem(likedKey, '1');
						} catch (e) {}
					}
				})
				.catch(function () {});
		});
	}

	/* 分享：复制链接（剪贴板 API → execCommand 兜底） */
	var shareBtn = document.querySelector('.np-action-share');
	if (shareBtn) {
		shareBtn.addEventListener('click', function () {
			var url = window.location.href;
			var flash = function (ok) {
				var old = shareBtn.textContent;
				shareBtn.textContent = ok ? '链接已复制' : '复制失败，请复制地址栏';
				setTimeout(function () {
					shareBtn.textContent = old;
				}, 1600);
			};
			var legacyCopy = function () {
				var ok = false;
				var ta = document.createElement('textarea');
				ta.value = url;
				ta.style.position = 'fixed';
				ta.style.opacity = '0';
				document.body.appendChild(ta);
				ta.select();
				try {
					ok = document.execCommand('copy');
				} catch (e) {}
				document.body.removeChild(ta);
				return ok;
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(function () {
					flash(true);
				}).catch(function () {
					flash(legacyCopy());
				});
			} else {
				flash(legacyCopy());
			}
		});
	}
})();

/**
 * 首页高度配平（两处）：
 * 1) 各本地板块 [[最新资讯]] 固定前 8 条，[[更多资讯]] 候选池扩到 16 条，
 *    按需露出条数使右列 ≈ 左列，矮列不再被拉伸空白、也不补占位板。
 * 2) TODAY 板块：左列(热点要闻列表) 与 右列(轮播+广告) 高度配平，
 *    列表按需露出（默认 12 条，最多 22），避免左列矮一截或右列下方空白。
 */
(function () {
	'use strict';

	// 本地板块首条是「图文卡」(display:block)，其余是「列表行」(display:flex)
	function showRow(el, i) {
		el.style.display = (i === 0) ? 'block' : 'flex';
	}

	function npBalanceCols() {
		var sections = document.querySelectorAll('.np-local-cols');
		for (var s = 0; s < sections.length; s++) {
			var cols = sections[s];
			var left = cols.querySelector('.np-local-focus-col');
			var right = cols.querySelector('.np-local-zixun-col');
			if (!left || !right) continue;
			var list = right.querySelector('.np-local-zixun');
			if (!list) continue;
			var rows = list.querySelectorAll('.wp-block-post');
			if (!rows.length) continue;

			// 先全部收起，量出左列真实内容高度
			for (var k = 0; k < rows.length; k++) rows[k].style.display = 'none';
			var leftH = left.getBoundingClientRect().height;
			var TOL = 6; // 容差：右列略矮 6px 内视为齐平
			var shown = 0;

			for (var i = 0; i < rows.length; i++) {
				showRow(rows[i], i);
				shown++;
				if (right.getBoundingClientRect().height >= leftH - TOL) break;
			}

			// 保底：至少露出 4 条，避免极矮时只露 1 张图卡显得空
			while (shown < 4 && shown < rows.length) {
				showRow(rows[shown], shown);
				shown++;
			}
		}
	}

	// TODAY 板块：左列热点要闻列表 配平 右列(轮播 + 广告)
	function npBalancePortal() {
		var portal = document.querySelector('.np-portal');
		if (!portal) return;
		var left = portal.querySelector('.np-col-left');
		var right = portal.querySelector('.np-col-right');
		var list = left ? left.querySelector('.np-today-list') : null;
		if (!left || !right || !list) return;
		var rows = list.querySelectorAll('.wp-block-post');
		if (!rows.length) return;

		// 收起全部，量出右列（目标）高度
		for (var k = 0; k < rows.length; k++) rows[k].style.display = 'none';
		var rightH = right.getBoundingClientRect().height;
		var TOL = 6;
		var shown = 0;

		for (var i = 0; i < rows.length; i++) {
			rows[i].style.display = 'flex'; // TODAY 全是列表行
			shown++;
			if (left.getBoundingClientRect().height >= rightH - TOL) break;
		}

		// 保底：至少露出 6 条
		while (shown < 6 && shown < rows.length) {
			rows[shown].style.display = 'flex';
			shown++;
		}
	}

	function init() {
		npBalanceCols();
		npBalancePortal();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
	// 图片/字体加载完高度可能微调，再配平一次
	window.addEventListener('load', init);
	if (document.fonts && document.fonts.ready) {
		document.fonts.ready.then(init);
	}
	// 轮播/广告等懒加载图片加载完会改变右列高度，加载后重算
	var imT;
	function onImgLoad() {
		clearTimeout(imT);
		imT = setTimeout(init, 120);
	}
	var imgs = document.querySelectorAll('.np-portal img');
	for (var n = 0; n < imgs.length; n++) {
		if (!imgs[n].complete) imgs[n].addEventListener('load', onImgLoad);
		imgs[n].addEventListener('error', onImgLoad);
	}
	// 兜底：延迟再配平，覆盖懒加载/异步广告导致的首屏误测
	setTimeout(init, 1500);
	setTimeout(init, 3000);
	// 视口变化（旋转/缩放）后重新配平
	var rt;
	window.addEventListener('resize', function () {
		clearTimeout(rt);
		rt = setTimeout(init, 200);
	});
})();
