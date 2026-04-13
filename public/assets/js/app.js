(function () {
	var body = document.body;
	var openBtn = document.querySelector('[data-mobile-open]');
	var closeBtn = document.querySelector('[data-mobile-close]');
	var overlay = document.querySelector('[data-mobile-overlay]');
	var drawer = document.querySelector('[data-mobile-drawer]');

	function eachNode(nodeList, callback) {
		for (var i = 0; i < nodeList.length; i += 1) {
			callback(nodeList[i], i);
		}
	}

	function closeDrawer() {
		if (!drawer) {
			return;
		}
		body.classList.remove('ss-drawer-open');
	}

	function openDrawer() {
		if (!drawer) {
			return;
		}
		body.classList.add('ss-drawer-open');
	}

	if (openBtn) {
		openBtn.addEventListener('click', openDrawer);
	}

	if (closeBtn) {
		closeBtn.addEventListener('click', closeDrawer);
	}

	if (overlay) {
		overlay.addEventListener('click', closeDrawer);
	}

	if (drawer) {
		eachNode(drawer.querySelectorAll('a'), function (link) {
			link.addEventListener('click', closeDrawer);
		});
	}

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape') {
			closeDrawer();
		}
	});

	var topBtn = document.querySelector('[data-back-to-top]');

	function handleScroll() {
		if (!topBtn) {
			return;
		}

		if (window.scrollY > 280) {
			topBtn.classList.add('is-visible');
		} else {
			topBtn.classList.remove('is-visible');
		}
	}

	handleScroll();
	window.addEventListener('scroll', handleScroll, { passive: true });

	if (topBtn) {
		topBtn.addEventListener('click', function () {
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		});
	}

	var yearNode = document.getElementById('ss-year');
	if (yearNode) {
		yearNode.textContent = String(new Date().getFullYear());
	}

	var popovers = document.querySelectorAll('[data-popover]');
	if (popovers.length > 0) {
		function closeAllPopovers(exceptGroup) {
			eachNode(popovers, function (group) {
				if (exceptGroup && group === exceptGroup) {
					return;
				}

				var trigger = group.querySelector('[data-popover-trigger]');
				var panel = group.querySelector('[data-popover-panel]');
				if (!trigger || !panel) {
					return;
				}

				trigger.setAttribute('aria-expanded', 'false');
				panel.hidden = true;
			});
		}

		eachNode(popovers, function (group) {
			var trigger = group.querySelector('[data-popover-trigger]');
			var panel = group.querySelector('[data-popover-panel]');
			if (!trigger || !panel) {
				return;
			}

			trigger.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();

				var willOpen = trigger.getAttribute('aria-expanded') !== 'true';
				closeAllPopovers(group);
				trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
				panel.hidden = !willOpen;
			});
		});

		document.addEventListener('click', function (event) {
			var clickedInside = false;
			eachNode(popovers, function (group) {
				if (!clickedInside && group.contains(event.target)) {
					clickedInside = true;
				}
			});

			if (!clickedInside) {
				closeAllPopovers();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeAllPopovers();
			}
		});
	}
})();
