/* Media picker for image and gallery fields. */
jQuery(function ($) {
	$(document).on('click', '.sdc-media__pick', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.sdc-media');
		var multiple = $box.data('multiple') === 1;
		var frame = wp.media({ multiple: multiple ? 'add' : false, library: { type: 'image' } });

		frame.on('open', function () {
			var selection = frame.state().get('selection');
			String($box.find('input').val()).split(',').filter(Boolean).forEach(function (id) {
				selection.add(wp.media.attachment(id));
			});
		});

		frame.on('select', function () {
			var items = frame.state().get('selection').toJSON();
			$box.find('input').val(items.map(function (a) { return a.id; }).join(','));
			$box.find('.sdc-media__preview').html(items.map(function (a) {
				var size = (a.sizes && (a.sizes.thumbnail || a.sizes.medium)) || a;
				return $('<img alt="">').attr('src', size.url).prop('outerHTML');
			}).join(''));
		});

		frame.open();
	});

	$(document).on('click', '.sdc-media__clear', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.sdc-media');
		$box.find('input').val('');
		$box.find('.sdc-media__preview').empty();
	});
});
