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

			if (input.parentElement && input.parentElement.classList.contains('ss-password-field')) {
				return;
			}

			var parent = input.parentElement;
			if (!parent) {
				return;
			}

			var wrapper = document.createElement('div');
			wrapper.className = 'ss-password-field';

			parent.insertBefore(wrapper, input);
			wrapper.appendChild(input);

			var toggle = document.createElement('button');
			toggle.type = 'button';
			toggle.className = 'ss-password-toggle';
			toggle.setAttribute('aria-label', 'Show password');
			toggle.setAttribute('title', 'Show password');
			toggle.innerHTML = [
				'<svg class="ss-password-show" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">',
				'<path d="M2.75 12C4.43 8.53 7.95 6.3 12 6.3C16.05 6.3 19.57 8.53 21.25 12C19.57 15.47 16.05 17.7 12 17.7C7.95 17.7 4.43 15.47 2.75 12Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
				'<circle cx="12" cy="12" r="2.45" stroke="currentColor" stroke-width="1.6"/>',
				'</svg>',
				'<svg class="ss-password-hide" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">',
				'<path d="M4.5 4.5L19.5 19.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
				'<path d="M2.75 12C4.43 8.53 7.95 6.3 12 6.3C16.05 6.3 19.57 8.53 21.25 12C19.57 15.47 16.05 17.7 12 17.7C7.95 17.7 4.43 15.47 2.75 12Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
				'</svg>'
			].join('');

			wrapper.appendChild(toggle);

			toggle.addEventListener('click', function () {
				var reveal = input.type === 'password';
				input.type = reveal ? 'text' : 'password';
				toggle.classList.toggle('is-visible', reveal);
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

			if (field.classList && field.classList.contains('ss-file-input-hidden')) {
				return false;
			}

			return true;
		}

		function resolveFieldLabel(field) {
			if (!field) {
				return 'This field';
			}

			var container = field.closest('.ss-field');
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

		function clearFieldError(field) {
			field.classList.remove('is-invalid');
			field.removeAttribute('aria-invalid');

			var container = field.closest('.ss-field') || field.parentElement;
			if (!container) {
				return;
			}

			var errorNode = container.querySelector('.ss-field-error');
			if (errorNode) {
				errorNode.remove();
			}
		}

		function setFieldError(field, message) {
			field.classList.add('is-invalid');
			field.setAttribute('aria-invalid', 'true');

			var container = field.closest('.ss-field') || field.parentElement;
			if (!container) {
				return;
			}

			var errorNode = container.querySelector('.ss-field-error');
			if (!errorNode) {
				errorNode = document.createElement('p');
				errorNode.className = 'ss-field-error';
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
					if (field.classList.contains('is-invalid')) {
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
					var firstInvalid = form.querySelector('.is-invalid');
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

		var trackedControls = form.querySelectorAll(
			'input[name="name"], input[name="email"], input[name="password"], input[name="password_confirmation"], input[name="profile_picture"], input[name="remove_profile_picture"]'
		);

		function controlState(control) {
			if (control.type === 'file') {
				var files = control.files;
				if (!files || files.length === 0) {
					return '';
				}

				var selected = files[0];
				return [selected.name, selected.size, selected.lastModified].join(':');
			}

			return String(control.value || '');
		}

		function snapshot() {
			var parts = [];
			eachNode(trackedControls, function (control) {
				parts.push(control.name + '=' + controlState(control));
			});

			return parts.join('|');
		}

		var initialState = snapshot();

		function updateDirtyState() {
			var dirty = snapshot() !== initialState;
			form.setAttribute('data-form-dirty', dirty ? 'true' : 'false');
			submitButton.disabled = !dirty;
		}

		form.setAttribute('data-form-dirty', 'false');
		submitButton.disabled = true;

		form.addEventListener('input', updateDirtyState);
		form.addEventListener('change', updateDirtyState);
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

	initPasswordToggles();
	initFormFieldValidation();
	initProfileSettingsDirtyState();
	initSubmitOnceForms();
	initProfilePhotoControls();
})();
