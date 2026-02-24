(function($) {
	'use strict';

	var WPEA_Pro = {
		init: function() {
			this.initMapView();
			this.initWeekView();
			this.initFilters();
		},

		initMapView: function() {
			var mapElement = document.getElementById('wpea-map');
			if (!mapElement) return;

			var eventsData = JSON.parse(mapElement.getAttribute('data-events') || '[]');

			if (eventsData.length === 0) return;

			var firstEvent = eventsData[0];
			var mapCenter = [firstEvent.lat, firstEvent.lon];

			if (typeof L !== 'undefined') {
				var map = L.map('wpea-map').setView(mapCenter, 12);

				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '© OpenStreetMap contributors',
					maxZoom: 18
				}).addTo(map);

				eventsData.forEach(function(event) {
					var marker = L.marker([event.lat, event.lon]).addTo(map);

					var popupContent = '<div class="wpea-map-popup">' +
						'<h3><a href="' + event.url + '">' + event.title + '</a></h3>';

					if (event.thumbnail) {
						popupContent += '<img src="' + event.thumbnail + '" alt="' + event.title + '" style="width:100%;border-radius:4px;margin-bottom:10px;">';
					}

					popupContent += '<p><strong>' + event.venue + '</strong><br>' +
						event.address + '<br>' +
						event.date + '</p>' +
						'</div>';

					marker.bindPopup(popupContent);

					$(document).on('click', '[data-event-id="' + event.id + '"]', function() {
						map.setView([event.lat, event.lon], 15);
						marker.openPopup();
					});
				});

				var group = new L.featureGroup(eventsData.map(function(event) {
					return L.marker([event.lat, event.lon]);
				}));
				map.fitBounds(group.getBounds().pad(0.1));
			} else {
				console.warn('Leaflet library not loaded. Map view requires Leaflet.js');
				mapElement.innerHTML = '<p style="padding:20px;text-align:center;">Map library not loaded. Please include Leaflet.js to use map view.</p>';
			}
		},

		initWeekView: function() {
			$('.wpea-week-prev, .wpea-week-next').on('click', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var currentWeek = parseInt($btn.data('week'));
				var direction = $btn.hasClass('wpea-week-prev') ? -1 : 1;
				var newWeek = currentWeek + (direction * 7 * 24 * 60 * 60);

				var $container = $btn.closest('.wpea_frontend_archive');
				var shortcode = JSON.parse($container.data('shortcode'));
				shortcode.week_start = newWeek;

				WPEA_Pro.loadWeekView(shortcode, $container);
			});
		},

		loadWeekView: function(shortcode, $container) {
			$.ajax({
				url: wpea_ajax.ajaxurl,
				type: 'POST',
				data: {
					action: 'wpea_load_week_view',
					shortcode: shortcode,
					nonce: wpea_ajax.nonce
				},
				beforeSend: function() {
					$container.addClass('wpea-loading');
				},
				success: function(response) {
					if (response.success) {
						$container.html(response.data.html);
						WPEA_Pro.initWeekView();
					}
				},
				complete: function() {
					$container.removeClass('wpea-loading');
				}
			});
		},

		initFilters: function() {
			$('.wpea-filter-form').on('submit', function(e) {
				var $form = $(this);
				var hasValues = false;

				$form.find('input, select').each(function() {
					if ($(this).val()) {
						hasValues = true;
						return false;
					}
				});

				if (!hasValues) {
					e.preventDefault();
					alert('Please select at least one filter.');
				}
			});
		}
	};

	$(document).ready(function() {
		WPEA_Pro.init();
	});

})(jQuery);
