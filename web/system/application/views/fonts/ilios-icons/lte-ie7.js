/* Use this script if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'ilios-icons\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-home' : '&#xe00f;',
			'icon-edit' : '&#x270e;',
			'icon-search' : '&#x26b2;',
			'icon-actions' : '&#xe015;',
			'icon-remove' : '&#xe016;',
			'icon-download' : '&#x2193;',
			'icon-upload' : '&#x2191;',
			'icon-link' : '&#x26ad;',
			'icon-unlink' : '&#x26ae;',
			'icon-attach' : '&#xe013;',
			'icon-star' : '&#xe011;',
			'icon-plus' : '&#xe001;',
			'icon-minus' : '&#xe01e;',
			'icon-help' : '&#xe000;',
			'icon-info' : '&#x2014;',
			'icon-blocked' : '&#x26d4;',
			'icon-cancel' : '&#x2715;',
			'icon-cancel-2' : '&#x78;',
			'icon-checkmark' : '&#x2714;',
			'icon-plus-2' : '&#x2b;',
			'icon-minus-2' : '&#x2013;',
			'icon-document' : '&#xe006;',
			'icon-file-pdf' : '&#xe005;',
			'icon-file-word' : '&#xe003;',
			'icon-file-excel' : '&#xe004;',
			'icon-file-powerpoint' : '&#xe007;',
			'icon-file-zip' : '&#xe00b;',
			'icon-picture' : '&#xe00c;',
			'icon-film' : '&#xe00d;',
			'icon-graph' : '&#xe00e;',
			'icon-music' : '&#x266b;',
			'icon-microphone' : '&#xe019;',
			'icon-arrow-down' : '&#x25bc;',
			'icon-arrow-up' : '&#x25b2;',
			'icon-arrow-left' : '&#x25c0;',
			'icon-arrow-right' : '&#x25b6;',
			'icon-expand' : '&#xe01a;',
			'icon-location' : '&#xe01b;',
			'icon-clock' : '&#xe01c;',
			'icon-warning' : '&#x2757;',
			'icon-question' : '&#x3f;',
			'icon-printer' : '&#xe020;',
			'icon-camera' : '&#xe012;',
			'icon-user' : '&#xe014;',
			'icon-users' : '&#xe01d;',
			'icon-add-user' : '&#xe01f;',
			'icon-locked' : '&#xe018;'
		},
		els = document.getElementsByTagName('*'),
		i, attr, html, c, el;
	for (i = 0; i < els.length; i += 1) {
		el = els[i];
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c) {
			addIcon(el, icons[c[0]]);
		}
	}
};