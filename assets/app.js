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
