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

	function escapeHtml(value) {
		return String(value == null ? '' : value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	function normalizeNotificationLink(value) {
		var link = String(value == null ? '' : value).trim();
		if (link === '' || link.charAt(0) !== '/') {
			return '/home';
		}

		return link;
	}

	function formatNotificationTime(value) {
		var rawValue = String(value == null ? '' : value).trim();
		if (rawValue === '') {
			return '';
		}

		var parsed = new Date(rawValue.replace(' ', 'T'));
		if (isNaN(parsed.getTime())) {
			return rawValue;
		}

		try {
			return new Intl.DateTimeFormat('en-US', {
				month: 'short',
				day: 'numeric',
				hour: '2-digit',
				minute: '2-digit',
				hour12: false
			}).format(parsed);
		} catch (error) {
			return rawValue;
		}
	}

	function getCurrentRelativeUrl() {
		var path = window.location.pathname || '/home';
		var search = window.location.search || '';
		var target = path + search;

		if (target === '') {
			return '/home';
		}

		return target.charAt(0) === '/' ? target : '/home';
	}

	function buildNotificationItemMarkup(notification, csrfToken, redirectTo) {
		var id = Number(notification && notification.id ? notification.id : 0);
		if (id <= 0) {
			return '';
		}

		var title = escapeHtml(notification && notification.title ? notification.title : 'Notification');
		var body = String(notification && notification.body ? notification.body : '');
		var link = escapeHtml(normalizeNotificationLink(notification ? notification.link : ''));
		var createdAt = escapeHtml(formatNotificationTime(notification ? notification.created_at : ''));
		var isUnread = String(notification && notification.read_at ? notification.read_at : '') === '';
		var itemClasses = isUnread
			? 'relative overflow-hidden rounded-xl border border-brand-200 bg-surface-page p-3 transition hover:border-brand-400 hover:bg-brand-50/40'
			: 'relative overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50 p-3 transition hover:border-brand-300 hover:bg-white';
		var bodyMarkup = body !== ''
			? '<p id="notif-body-' + id + '" class="mt-1 text-sm leading-6 text-neutral-700">' + escapeHtml(body) + '</p>'
			: '';
		var describedBy = 'notif-meta-' + id + (body !== '' ? ' notif-body-' + id : '');
		var markReadMarkup = isUnread
			? '<div class="relative z-20 mt-2"><form action="/notifications/' + id + '/read" method="post">' +
				'<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">' +
				'<input type="hidden" name="redirect" value="' + escapeHtml(redirectTo) + '">' +
				'<button type="submit" class="text-xs font-bold text-neutral-700 transition hover:text-brand-700">Mark read</button>' +
			'</form></div>'
			: '';

		return [
			'<li class="' + itemClasses + '">',
				'<a href="' + link + '" class="absolute inset-0 z-10 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-200 focus-visible:ring-offset-2 focus-visible:ring-offset-white" aria-labelledby="notif-title-' + id + '" aria-describedby="' + escapeHtml(describedBy) + '">',
					'<span class="sr-only">Open notification</span>',
				'</a>',
				'<div class="relative z-0">',
					'<div class="flex items-start justify-between gap-2">',
						'<p id="notif-title-' + id + '" class="text-sm font-extrabold text-neutral-900">' + title + '</p>',
						'<time id="notif-meta-' + id + '" class="text-xs font-semibold text-neutral-500">' + createdAt + '</time>',
					'</div>',
					bodyMarkup,
				'</div>',
				markReadMarkup,
			'</li>'
		].join('');
	}

	function buildNotificationFeedMarkup(feed, csrfToken, redirectTo) {
		var items = feed && feed.items && feed.items.length ? feed.items : [];
		if (items.length === 0) {
			return '<p class="rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-4 text-sm text-neutral-600">No new notifications yet.</p>';
		}

		var markup = '<ul class="grid max-h-[24rem] gap-2 overflow-y-auto pr-1">';
		eachNode(items, function (notification) {
			markup += buildNotificationItemMarkup(notification, csrfToken, redirectTo);
		});
		markup += '</ul>';
		return markup;
	}

	function initNotificationPolling() {
		var popover = document.querySelector('[data-notif-popover]');
		if (!popover) {
			return;
		}

		var panel = popover.querySelector('[data-notif-panel]');
		var trigger = popover.querySelector('[data-notif-toggle]');
		var badge = popover.querySelector('[data-notif-count]');
		var markAllWrap = popover.querySelector('[data-notif-mark-all-wrap]');
		var feedBody = popover.querySelector('[data-notif-feed-body]');
		var feedUrl = popover.getAttribute('data-notif-feed-url') || '/notifications?format=json&limit=6';
		var csrfToken = popover.getAttribute('data-notif-csrf-token') || '';
		var redirectTo = getCurrentRelativeUrl();
		var pollInterval = 30000;
		var requestInFlight = false;
		var lastUnreadCount = null;

		function updateBadge(unreadCount) {
			var count = parseInt(unreadCount, 10);
			if (isNaN(count) || count < 0) {
				count = 0;
			}

			if (badge) {
				badge.textContent = count > 0 ? (count > 99 ? '99+' : String(count)) : '';
				badge.style.display = count > 0 ? 'inline-flex' : 'none';
			}

			if (markAllWrap) {
				markAllWrap.hidden = count <= 0;
			}
		}

		function renderFeed(feed) {
			if (!feedBody) {
				return;
			}

			feedBody.innerHTML = buildNotificationFeedMarkup(feed, csrfToken, redirectTo);
		}

		function fetchFeed(forceRender) {
			if (requestInFlight) {
				return;
			}

			if (document.hidden && !forceRender) {
				return;
			}

			requestInFlight = true;

			fetch(feedUrl, {
				credentials: 'same-origin',
				headers: {
					Accept: 'application/json'
				}
			})
				.then(function (response) {
					if (!response.ok) {
						throw new Error('Notification polling failed.');
					}

					return response.json();
				})
				.then(function (payload) {
					var feed = payload && payload.data ? payload.data : payload;
					if (!feed || typeof feed !== 'object') {
						return;
					}

					var previousUnreadCount = lastUnreadCount;
					var unreadCount = parseInt(feed.unread_count, 10);
					if (isNaN(unreadCount) || unreadCount < 0) {
						unreadCount = 0;
					}

					updateBadge(unreadCount);

					if (feedBody && (forceRender || !panel || panel.hidden || previousUnreadCount === null || unreadCount !== previousUnreadCount)) {
						renderFeed(feed);
					}

					lastUnreadCount = unreadCount;
				})
				.catch(function () {
					return null;
				})
				.then(function () {
					requestInFlight = false;
				});
		}

		updateBadge(badge ? badge.textContent : 0);
		fetchFeed(false);

		window.setInterval(function () {
			fetchFeed(false);
		}, pollInterval);

		document.addEventListener('visibilitychange', function () {
			if (!document.hidden) {
				fetchFeed(false);
			}
		});

		if (trigger) {
			trigger.addEventListener('click', function () {
				window.setTimeout(function () {
					if (panel && !panel.hidden) {
						fetchFeed(true);
					}
				}, 0);
			});
		}
	}

	function initGuestNavHighlight() {
		var guestLinks = document.querySelectorAll('[data-guest-nav-link]');
		if (guestLinks.length === 0) {
			return;
		}

		var path = window.location.pathname;
		if (path !== '/services' && path !== '/') {
			return;
		}

		var activeClasses = 'rounded-full bg-brand-100 px-4 py-2.5 text-base font-extrabold text-brand-700';
		var inactiveClasses = 'rounded-full px-4 py-2.5 text-base font-extrabold text-neutral-600 transition hover:bg-brand-50 hover:text-brand-700';
		var sections = {};

		eachNode(guestLinks, function (link) {
			var section = link.getAttribute('data-guest-nav-section');
			if (section) {
				sections[section] = true;
			}
		});

		function setActive(section) {
			eachNode(guestLinks, function (link) {
				var linkSection = link.getAttribute('data-guest-nav-section');
				var isActive = linkSection === section;
				link.className = isActive ? activeClasses : inactiveClasses;
			});
		}

		function resolveSection() {
			var hash = window.location.hash || '';
			var section = hash !== '' ? hash.slice(1) : 'home';
			if (!sections[section]) {
				section = 'home';
			}
			return section;
		}

		function handleHashChange() {
			setActive(resolveSection());
		}

		handleHashChange();
		window.addEventListener('hashchange', handleHashChange);
	}

	function closeDrawer() {
		if (overlay) {
			overlay.classList.add('opacity-0', 'pointer-events-none');
			overlay.classList.remove('opacity-100');
		}

		if (drawer) {
			drawer.classList.add('translate-x-full', 'pointer-events-none');
			drawer.classList.remove('translate-x-0');
		}

		body.classList.remove('overflow-hidden');
	}

	function openDrawer() {
		if (overlay) {
			overlay.classList.remove('opacity-0', 'pointer-events-none');
			overlay.classList.add('opacity-100');
		}

		if (drawer) {
			drawer.classList.remove('translate-x-full', 'pointer-events-none');
			drawer.classList.add('translate-x-0');
		}

		body.classList.add('overflow-hidden');
	}

	if (openBtn) {
		openBtn.addEventListener('click', openDrawer);
	}

	initGuestNavHighlight();

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
			topBtn.classList.remove('opacity-0', 'translate-y-2', 'pointer-events-none');
			topBtn.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
		} else {
			topBtn.classList.add('opacity-0', 'translate-y-2', 'pointer-events-none');
			topBtn.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
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

	var yearNodes = document.querySelectorAll('[data-current-year]');
	if (yearNodes.length > 0) {
		var currentYear = String(new Date().getFullYear());
		eachNode(yearNodes, function (yearNode) {
			yearNode.textContent = currentYear;
		});
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

	function initSubmitOnceForms() {
		var forms = document.querySelectorAll('form[data-submit-once]');
		if (forms.length === 0) {
			return;
		}

		eachNode(forms, function (form) {
			var isSubmitting = false;
			form.addEventListener('submit', function (event) {
				if (event.defaultPrevented) {
					return;
				}

				var requiresDirty = form.getAttribute('data-submit-requires-dirty') === 'true';
				var isDirty = form.getAttribute('data-form-dirty') === 'true';

				if (requiresDirty && !isDirty) {
					event.preventDefault();
					return;
				}

				if (isSubmitting) {
					event.preventDefault();
					return;
				}

				isSubmitting = true;

				eachNode(form.querySelectorAll('button[type="submit"], input[type="submit"]'), function (submitControl) {
					if (submitControl.disabled) {
						return;
					}

					if (submitControl.tagName === 'BUTTON') {
						var loadingText = submitControl.getAttribute('data-loading-text');
						if (loadingText && loadingText !== '') {
							submitControl.textContent = loadingText;
						}
					}

					submitControl.disabled = true;
				});
			});
		});
	}

	function initPasswordToggles() {
		var passwordInputs = document.querySelectorAll('input[type="password"]');
		if (passwordInputs.length === 0) {
			return;
		}

		eachNode(passwordInputs, function (input) {
			if (input.hasAttribute('data-no-password-toggle')) {
				return;
			}

			if (input.parentElement && input.parentElement.hasAttribute('data-password-field')) {
				return;
			}

			var parent = input.parentElement;
			if (!parent) {
				return;
			}

			var wrapper = document.createElement('div');
			wrapper.className = 'relative';
			wrapper.setAttribute('data-password-field', '');

			parent.insertBefore(wrapper, input);
			wrapper.appendChild(input);
			input.classList.add('pr-12');

			var toggle = document.createElement('button');
			toggle.type = 'button';
			toggle.className = 'absolute right-3 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-neutral-300 bg-white text-neutral-600 transition hover:border-brand-500 hover:text-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-100';
			toggle.setAttribute('data-password-toggle', '');
			toggle.setAttribute('aria-label', 'Show password');
			toggle.setAttribute('title', 'Show password');
			toggle.innerHTML = [
				'<svg data-password-icon-show viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4">',
				'<path d="M2.75 12C4.43 8.53 7.95 6.3 12 6.3C16.05 6.3 19.57 8.53 21.25 12C19.57 15.47 16.05 17.7 12 17.7C7.95 17.7 4.43 15.47 2.75 12Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
				'<circle cx="12" cy="12" r="2.45" stroke="currentColor" stroke-width="1.6"/>',
				'</svg>',
				'<svg data-password-icon-hide viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="hidden h-4 w-4">',
				'<path d="M4.5 4.5L19.5 19.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
				'<path d="M2.75 12C4.43 8.53 7.95 6.3 12 6.3C16.05 6.3 19.57 8.53 21.25 12C19.57 15.47 16.05 17.7 12 17.7C7.95 17.7 4.43 15.47 2.75 12Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
				'</svg>'
			].join('');

			wrapper.appendChild(toggle);
			var showIcon = toggle.querySelector('[data-password-icon-show]');
			var hideIcon = toggle.querySelector('[data-password-icon-hide]');

			toggle.addEventListener('click', function () {
				var reveal = input.type === 'password';
				input.type = reveal ? 'text' : 'password';
				if (showIcon && hideIcon) {
					showIcon.classList.toggle('hidden', reveal);
					hideIcon.classList.toggle('hidden', !reveal);
				}
				toggle.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
				toggle.setAttribute('title', reveal ? 'Hide password' : 'Show password');
			});
		});
	}

	function initFormFieldValidation() {
		var forms = document.querySelectorAll('form');
		if (forms.length === 0) {
			return;
		}

		function shouldValidateField(field) {
			if (!field || field.disabled || field.hasAttribute('data-skip-validation')) {
				return false;
			}

			var type = (field.type || '').toLowerCase();
			if (type === 'hidden' || type === 'button' || type === 'submit' || type === 'reset' || type === 'image') {
				return false;
			}

			if (field.hasAttribute('data-file-input-hidden')) {
				return false;
			}

			return true;
		}

		function resolveFieldLabel(field) {
			if (!field) {
				return 'This field';
			}

			var container = field.closest('[data-field]');
			if (container) {
				var label = container.querySelector('label');
				if (label && label.textContent) {
					return label.textContent.replace(/\s+/g, ' ').trim().replace(/:$/, '');
				}
			}

			var name = field.getAttribute('name') || field.id || 'This field';
			return name.replace(/_/g, ' ');
		}

		function resolveValidationMessage(field) {
			var label = resolveFieldLabel(field);
			var validity = field.validity;

			if (validity.valueMissing) {
				return label + ' is required.';
			}

			if (validity.typeMismatch && field.type === 'email') {
				return 'Please enter a valid email address.';
			}

			if (validity.tooShort && field.minLength > 0) {
				return label + ' must be at least ' + String(field.minLength) + ' characters.';
			}

			if (validity.tooLong && field.maxLength > 0) {
				return label + ' must be at most ' + String(field.maxLength) + ' characters.';
			}

			if (validity.patternMismatch) {
				var patternMessage = field.getAttribute('data-pattern-message');
				if (patternMessage && patternMessage !== '') {
					return patternMessage;
				}

				return label + ' has an invalid format.';
			}

			if (field.validationMessage && field.validationMessage !== '') {
				return field.validationMessage;
			}

			return label + ' is invalid.';
		}

		function resolveFieldContainer(field) {
			if (!field) {
				return null;
			}

			var container = field.closest('[data-field]');
			if (container) {
				return container;
			}

			container = field.parentElement;
			if (container && container.hasAttribute('data-password-field')) {
				return container.parentElement || container;
			}

			return container;
		}

		function clearFieldError(field) {
			field.classList.remove('is-invalid');
			field.removeAttribute('data-invalid');
			field.removeAttribute('aria-invalid');

			var container = resolveFieldContainer(field);
			if (!container) {
				return;
			}

			var errorNode = container.querySelector('[data-field-error]');
			if (errorNode) {
				errorNode.remove();
			}
		}

		function setFieldError(field, message) {
			field.classList.add('is-invalid');
			field.setAttribute('data-invalid', 'true');
			field.setAttribute('aria-invalid', 'true');

			var container = resolveFieldContainer(field);
			if (!container) {
				return;
			}

			var errorNode = container.querySelector('[data-field-error]');
			if (!errorNode) {
				errorNode = document.createElement('p');
				errorNode.className = 'mt-2 text-xs font-semibold text-danger-700';
				errorNode.setAttribute('data-field-error', '');
				container.appendChild(errorNode);
			}

			errorNode.textContent = message;
		}

		function validateField(field) {
			if (!shouldValidateField(field)) {
				return true;
			}

			var fieldName = String(field.getAttribute('name') || '').toLowerCase();
			if (fieldName === 'password_confirmation') {
				var form = field.form;
				var passwordField = form ? form.querySelector('input[name="password"]') : null;
				if (passwordField && field.value !== '' && passwordField.value !== field.value) {
					setFieldError(field, 'Password confirmation must match password.');
					return false;
				}
			}

			if (field.checkValidity()) {
				clearFieldError(field);
				return true;
			}

			setFieldError(field, resolveValidationMessage(field));
			return false;
		}

		eachNode(forms, function (form) {
			if (form.hasAttribute('data-skip-client-validation')) {
				return;
			}

			form.setAttribute('novalidate', 'novalidate');

			eachNode(form.querySelectorAll('input, select, textarea'), function (field) {
				if (!shouldValidateField(field)) {
					return;
				}

				var fieldName = String(field.getAttribute('name') || '').toLowerCase();

				field.addEventListener('blur', function () {
					validateField(field);
				});

				field.addEventListener('input', function () {
					if (field.classList.contains('is-invalid') || field.hasAttribute('data-invalid')) {
						validateField(field);
					}

					if (fieldName === 'password') {
						var confirmation = form.querySelector('input[name="password_confirmation"]');
						if (confirmation && confirmation.value !== '') {
							validateField(confirmation);
						}
					}
				});

				field.addEventListener('change', function () {
					validateField(field);
				});
			});

			form.addEventListener('submit', function (event) {
				var isValid = true;
				eachNode(form.querySelectorAll('input, select, textarea'), function (field) {
					if (!validateField(field)) {
						isValid = false;
					}
				});

				if (!isValid) {
					event.preventDefault();
					var firstInvalid = form.querySelector('[data-invalid], .is-invalid');
					if (firstInvalid && typeof firstInvalid.focus === 'function') {
						firstInvalid.focus();
					}
				}
			});
		});
	}

	function initProfileSettingsDirtyState() {
		var form = document.getElementById('profile-settings-form');
		if (!form) {
			return;
		}

		var submitButton = form.querySelector('[data-submit-once-button]');
		if (!submitButton) {
			return;
		}

		var nameControl = form.querySelector('input[name="name"]');
		var emailControl = form.querySelector('input[name="email"]');
		var passwordControl = form.querySelector('input[name="password"]');
		var confirmationControl = form.querySelector('input[name="password_confirmation"]');
		var fileInput = form.querySelector('input[name="profile_picture"]');
		var removeField = form.querySelector('input[name="remove_profile_picture"]');
		var originalName = String(form.getAttribute('data-original-name') || '');
		var originalEmail = String(form.getAttribute('data-original-email') || '');

		function normalizeValue(value) {
			return String(value || '').trim();
		}

		function normalizeEmail(value) {
			return normalizeValue(value).toLowerCase();
		}

		function hasProfilePhotoChange() {
			if (fileInput && fileInput.files && fileInput.files.length > 0) {
				return true;
			}

			return Boolean(removeField && String(removeField.value || '') === '1');
		}

		function updateDirtyState() {
			var hasNameChanged = nameControl ? normalizeValue(nameControl.value) !== normalizeValue(originalName) : false;
			var hasEmailChanged = emailControl ? normalizeEmail(emailControl.value) !== normalizeEmail(originalEmail) : false;
			var hasPasswordChange = passwordControl ? normalizeValue(passwordControl.value) !== '' : false;
			var hasConfirmationChange = confirmationControl ? normalizeValue(confirmationControl.value) !== '' : false;
			var allowSubmit = hasNameChanged || hasEmailChanged || hasPasswordChange || hasConfirmationChange || hasProfilePhotoChange();
			form.setAttribute('data-form-dirty', allowSubmit ? 'true' : 'false');
			submitButton.disabled = !allowSubmit;
		}

		form.setAttribute('data-form-dirty', 'false');
		submitButton.disabled = true;
		form.addEventListener('input', updateDirtyState);
		form.addEventListener('change', updateDirtyState);
		updateDirtyState();
	}

	function initProfilePhotoControls() {
		var shell = document.querySelector('[data-profile-photo-shell]');
		if (!shell) {
			return;
		}

		var preview = shell.querySelector('[data-profile-photo-preview]');
		var fileInput = shell.querySelector('[data-profile-picture-input]');
		var removeField = shell.querySelector('[data-remove-profile-picture]');
		var hasCustomPhoto = shell.getAttribute('data-has-custom-photo') === '1';
		var trigger = shell.querySelector('[data-profile-photo-trigger]');
		var removeButton = shell.querySelector('[data-profile-photo-remove]');
		var objectUrl = null;

		if (!preview || !fileInput) {
			return;
		}

		function openPicker() {
			fileInput.click();
		}

		function setPreview(url) {
			if (url && url !== '') {
				preview.removeAttribute('data-fallback-applied');
				preview.src = url;
			}
		}

		function signalChange(target) {
			if (!target) {
				return;
			}

			target.dispatchEvent(new Event('input', { bubbles: true }));
			target.dispatchEvent(new Event('change', { bubbles: true }));
		}

		function resetToDefault() {
			var defaultSrc = preview.getAttribute('data-default-src') || '';
			if (defaultSrc !== '') {
				setPreview(defaultSrc);
			}
		}

		if (trigger) {
			trigger.addEventListener('click', openPicker);
		}

		fileInput.addEventListener('change', function () {
			var files = fileInput.files;
			var selectedFile = files && files.length > 0 ? files[0] : null;

			if (!selectedFile) {
				return;
			}

			if (removeField) {
				removeField.value = '0';
				signalChange(removeField);
			}

			if (objectUrl) {
				URL.revokeObjectURL(objectUrl);
			}

			objectUrl = URL.createObjectURL(selectedFile);
			setPreview(objectUrl);
		});

		if (removeButton) {
			removeButton.addEventListener('click', function () {
				var hasSelectedFile = fileInput.files && fileInput.files.length > 0;
				if (!hasCustomPhoto && !hasSelectedFile) {
					return;
				}

				if (!window.confirm('Remove your profile photo and switch to the default avatar?')) {
					return;
				}

				if (removeField) {
					removeField.value = hasCustomPhoto ? '1' : '0';
					signalChange(removeField);
				}

				fileInput.value = '';
				signalChange(fileInput);

				if (objectUrl) {
					URL.revokeObjectURL(objectUrl);
					objectUrl = null;
				}

				resetToDefault();
			});
		}

		window.addEventListener('beforeunload', function () {
			if (objectUrl) {
				URL.revokeObjectURL(objectUrl);
			}
		});
	}

	function initImageFallbacks(rootNode) {
		var searchRoot = rootNode && typeof rootNode.querySelectorAll === 'function' ? rootNode : document;
		var images = searchRoot.querySelectorAll('img[data-fallback-src]');
		if (images.length === 0) {
			return;
		}

		function applyFallback(image) {
			var fallbackSrc = String(image.getAttribute('data-fallback-src') || '').trim();
			if (fallbackSrc === '') {
				return;
			}

			if (image.getAttribute('data-fallback-applied') === '1') {
				return;
			}

			var currentSrc = String(image.getAttribute('src') || '').trim();
			if (currentSrc === fallbackSrc) {
				image.setAttribute('data-fallback-applied', '1');
				return;
			}

			image.setAttribute('data-fallback-applied', '1');
			image.src = fallbackSrc;
		}

		eachNode(images, function (image) {
			if (image.getAttribute('data-fallback-bound') !== '1') {
				image.setAttribute('data-fallback-bound', '1');
				image.addEventListener('error', function () {
					applyFallback(image);
				});
			}

			var src = String(image.getAttribute('src') || '').trim();
			if (src === '') {
				applyFallback(image);
				return;
			}

			if (image.complete && image.naturalWidth === 0) {
				applyFallback(image);
			}
		});
	}

	function initClickableCards(rootNode) {
		var searchRoot = rootNode && typeof rootNode.querySelectorAll === 'function' ? rootNode : document;
		var cards = searchRoot.querySelectorAll('[data-card-link]');
		if (cards.length === 0) {
			return;
		}

		function shouldIgnoreNavigation(target, card) {
			var node = target;
			while (node && node !== card) {
				if (!(node instanceof HTMLElement)) {
					return true;
				}

				var tagName = String(node.tagName || '').toUpperCase();
				if (
					tagName === 'A'
					|| tagName === 'BUTTON'
					|| tagName === 'INPUT'
					|| tagName === 'SELECT'
					|| tagName === 'TEXTAREA'
					|| tagName === 'LABEL'
					|| tagName === 'SUMMARY'
					|| node.hasAttribute('data-no-card-nav')
				) {
					return true;
				}

				node = node.parentElement;
			}

			return false;
		}

		eachNode(cards, function (card) {
			var href = card.getAttribute('data-card-link');
			if (!href || card.getAttribute('data-card-link-bound') === '1') {
				return;
			}

			card.setAttribute('data-card-link-bound', '1');

			if (!card.hasAttribute('tabindex')) {
				card.setAttribute('tabindex', '0');
			}

			if (!card.hasAttribute('role')) {
				card.setAttribute('role', 'link');
			}

			card.addEventListener('click', function (event) {
				if (event.defaultPrevented) {
					return;
				}

				if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
					return;
				}

				if (shouldIgnoreNavigation(event.target, card)) {
					return;
				}

				window.location.assign(href);
			});

			card.addEventListener('keydown', function (event) {
				if (event.key !== 'Enter' && event.key !== ' ') {
					return;
				}

				if (shouldIgnoreNavigation(event.target, card)) {
					return;
				}

				event.preventDefault();
				window.location.assign(href);
			});
		});
	}

	function initMarketplaceLiveSearch() {
		var form = document.querySelector('[data-live-search-form]');
		if (!form || typeof window.fetch !== 'function') {
			return;
		}

		var input = form.querySelector('[data-live-search-input]');
		var statusNode = form.querySelector('[data-live-search-status]');
		var resultsWrap = document.querySelector('[data-live-search-results-wrap]');
		var resultsGrid = resultsWrap ? resultsWrap.querySelector('[data-live-search-results]') : null;
		var emptyCard = resultsWrap ? resultsWrap.querySelector('[data-live-search-empty]') : null;

		if (!input || !resultsGrid || !emptyCard) {
			return;
		}

		var emptyTitle = emptyCard.querySelector('[data-live-search-empty-title]');
		var emptyCopy = emptyCard.querySelector('[data-live-search-empty-copy]');
		var emptyOfferLink = emptyCard.querySelector('[data-live-search-empty-offer]');

		var endpoint = form.getAttribute('data-live-search-endpoint') || '/services?format=json';
		var maxLength = parseInt(form.getAttribute('data-search-max-length') || '120', 10);
		var minLength = parseInt(input.getAttribute('data-live-search-min-length') || '2', 10);
		var debounceDelay = 260;

		if (!isFinite(maxLength) || maxLength <= 0) {
			maxLength = 120;
		}

		if (!isFinite(minLength) || minLength < 1) {
			minLength = 1;
		}

		var debounceHandle = null;
		var activeController = null;
		var activeSequence = 0;
		var lastDispatched = null;
		var queryCache = Object.create(null);
		var statusToneClasses = ['text-neutral-600', 'text-neutral-700', 'text-info-700', 'text-danger-700'];
		var statusToneClassMap = {
			'is-hint': 'text-neutral-600',
			'is-muted': 'text-neutral-700',
			'is-loading': 'text-info-700',
			'is-error': 'text-danger-700'
		};

		function normalizeQuery(rawValue) {
			var normalized = String(rawValue || '').replace(/\s+/g, ' ').trim();
			if (normalized.length > maxLength) {
				normalized = normalized.slice(0, maxLength);
			}

			return normalized;
		}

		function setStatus(message, toneClass) {
			if (!statusNode) {
				return;
			}

			statusNode.hidden = message === '';
			statusNode.textContent = message;
			eachNode(statusToneClasses, function (className) {
				statusNode.classList.remove(className);
			});

			if (toneClass && statusToneClassMap[toneClass]) {
				statusNode.classList.add(statusToneClassMap[toneClass]);
			}
		}

		function setOfferLinkLabel(label) {
			if (!emptyOfferLink) {
				return;
			}

			emptyOfferLink.innerHTML = label + ' <span aria-hidden="true">-></span>';
		}

		function showEmptyState(queryValue) {
			resultsGrid.hidden = true;
			emptyCard.hidden = false;

			if (queryValue === '') {
				if (emptyTitle) {
					emptyTitle.textContent = 'No active services yet.';
				}
				if (emptyCopy) {
					emptyCopy.textContent = 'Be the first to offer a skill and start swapping with the community.';
				}
				setOfferLinkLabel('Create first service');
				return;
			}

			if (emptyTitle) {
				emptyTitle.textContent = 'No skills found for "' + queryValue + '".';
			}
			if (emptyCopy) {
				emptyCopy.textContent = 'Try another keyword like design, editing, coding, tutoring, or language.';
			}
			setOfferLinkLabel('Offer this skill');
		}

		function createServiceCard(service, cardIndex) {
			var detailUrl = String(service.detail_url || '/services');
			var title = String(service.title || 'Skill Service');
			var providerName = String(service.provider_name || 'Provider');
			var providerImageUrl = String(service.provider_image_url || '/assets/images/default-avatar.png');
			var description = String(service.description || '');
			var priceLabel = String(service.price_label || 'PHP 0.00');
			var isOwnService = service.is_own_service === true;

			var article = document.createElement('article');
			article.className = 'group grid min-h-[17rem] gap-4 rounded-3xl border border-neutral-300 bg-white p-6 shadow-soft transition hover:-translate-y-1 hover:border-brand-200 hover:bg-surface-rose hover:shadow-brand';
			article.setAttribute('data-card-link', detailUrl);
			article.setAttribute('role', 'link');
			article.setAttribute('tabindex', '0');
			article.setAttribute('aria-label', 'Open ' + title + ' details');

			var topBlock = document.createElement('div');
			var heading = document.createElement('h3');
			heading.className = 'text-[1.45rem] font-extrabold leading-tight text-neutral-950';
			heading.textContent = title;
			topBlock.appendChild(heading);

			var providerRow = document.createElement('div');
			providerRow.className = 'mt-1.5 flex items-center gap-3';

			var avatar = document.createElement('img');
			avatar.src = providerImageUrl;
			avatar.setAttribute('data-fallback-src', '/assets/images/default-avatar.png');
			avatar.alt = providerName + ' profile picture';
			avatar.className = 'h-9 w-9 rounded-full border-2 border-brand-200 bg-neutral-200 object-cover';
			providerRow.appendChild(avatar);

			var providerText = document.createElement('p');
			providerText.className = 'text-sm font-bold text-neutral-700';
			providerText.textContent = 'By ' + providerName;
			providerRow.appendChild(providerText);

			topBlock.appendChild(providerRow);
			article.appendChild(topBlock);

			var descriptionNode = document.createElement('p');
			descriptionNode.className = 'text-sm leading-7 text-neutral-700';
			descriptionNode.textContent = description;
			article.appendChild(descriptionNode);

			var footNode = document.createElement('div');
			footNode.className = 'mt-auto flex items-center justify-between gap-3';

			var priceNode = document.createElement('span');
			priceNode.className = 'rounded-full bg-white/90 px-3 py-1 text-sm font-extrabold text-brand-700';
			priceNode.textContent = priceLabel;
			footNode.appendChild(priceNode);

			var actionsNode = document.createElement('div');
			actionsNode.className = 'flex items-center gap-3';

			var viewLink = document.createElement('a');
			viewLink.href = detailUrl;
			viewLink.className = 'inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700';
			viewLink.textContent = 'View';
			actionsNode.appendChild(viewLink);

			if (!isOwnService) {
				var bookLink = document.createElement('a');
				bookLink.href = String(service.book_url || detailUrl);
				bookLink.className = 'inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700';
				bookLink.textContent = 'Book';
				actionsNode.appendChild(bookLink);
			} else {
				var ownLabel = document.createElement('span');
				ownLabel.className = 'text-sm font-semibold text-neutral-600';
				ownLabel.textContent = 'Your service';
				actionsNode.appendChild(ownLabel);
			}

			footNode.appendChild(actionsNode);
			article.appendChild(footNode);

			return article;
		}

		function renderServices(services, queryValue) {
			if (!Array.isArray(services) || services.length === 0) {
				resultsGrid.innerHTML = '';
				showEmptyState(queryValue);
				if (queryValue === '') {
					setStatus('', 'is-hint');
				} else {
					setStatus('No matching skills for "' + queryValue + '".', 'is-muted');
				}
				return;
			}

			resultsGrid.innerHTML = '';
			eachNode(services, function (service, index) {
				resultsGrid.appendChild(createServiceCard(service, index));
			});

			resultsGrid.hidden = false;
			emptyCard.hidden = true;
			initImageFallbacks(resultsGrid);
			initClickableCards(resultsGrid);

			var count = services.length;
			if (queryValue === '') {
				setStatus('', 'is-hint');
			} else {
				setStatus('Showing ' + String(count) + ' result' + (count === 1 ? '' : 's') + ' for "' + queryValue + '".', 'is-muted');
			}
		}

		function buildRequestUrl(queryValue) {
			var separator = endpoint.indexOf('?') === -1 ? '?' : '&';
			return endpoint + separator + 'q=' + encodeURIComponent(queryValue);
		}

		function fetchAndRender(queryValue) {
			if (Object.prototype.hasOwnProperty.call(queryCache, queryValue)) {
				renderServices(queryCache[queryValue], queryValue);
				return;
			}

			if (activeController) {
				activeController.abort();
			}

			activeController = new AbortController();
			activeSequence += 1;
			var requestSequence = activeSequence;

			setStatus('Searching skills...', 'is-loading');

			fetch(buildRequestUrl(queryValue), {
				method: 'GET',
				headers: {
					Accept: 'application/json'
				},
				credentials: 'same-origin',
				signal: activeController.signal
			})
				.then(function (response) {
					if (!response.ok) {
						throw new Error('Search request failed with status ' + String(response.status));
					}
					return response.json();
				})
				.then(function (payload) {
					if (requestSequence !== activeSequence) {
						return;
					}

					if (!payload || !Array.isArray(payload.services)) {
						throw new Error('Search response format is invalid.');
					}

					queryCache[queryValue] = payload.services;
					renderServices(payload.services, queryValue);
				})
				.catch(function (error) {
					if (activeController && activeController.signal.aborted) {
						return;
					}

					if (requestSequence !== activeSequence) {
						return;
					}

					setStatus('Search is temporarily unavailable. Press Enter to retry full search.', 'is-error');
					if (window.console && typeof window.console.error === 'function') {
						window.console.error(error);
					}
				});
		}

		function runSearch(force) {
			var queryValue = normalizeQuery(input.value);
			if (queryValue !== input.value) {
				input.value = queryValue;
			}

			if (!force && queryValue === lastDispatched) {
				return;
			}

			lastDispatched = queryValue;

			if (!force && queryValue !== '' && queryValue.length < minLength) {
				setStatus('Type at least ' + String(minLength) + ' characters to search.', 'is-hint');
				return;
			}

			fetchAndRender(queryValue);
		}

		function clearSearch(event) {
			if (event) {
				event.preventDefault();
			}

			input.value = '';
			lastDispatched = null;
			if (debounceHandle) {
				clearTimeout(debounceHandle);
				debounceHandle = null;
			}

			runSearch(true);
			input.focus();
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			if (debounceHandle) {
				clearTimeout(debounceHandle);
				debounceHandle = null;
			}

			runSearch(true);
		});

		input.addEventListener('input', function () {
			if (debounceHandle) {
				clearTimeout(debounceHandle);
			}

			debounceHandle = window.setTimeout(function () {
				runSearch(false);
			}, debounceDelay);
		});

		input.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && normalizeQuery(input.value) !== '') {
				clearSearch(event);
			}
		});
	}

	initPasswordToggles();
	initNotificationPolling();
	initFormFieldValidation();
	initProfileSettingsDirtyState();
	initSubmitOnceForms();
	initProfilePhotoControls();
	initImageFallbacks();
	initClickableCards();
	initMarketplaceLiveSearch();
})();
