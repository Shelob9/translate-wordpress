
const init_url_translate = () => {
	const $ = jQuery;

	const execute = () => {
		let old_text = {}

		const edit_weglot_post_name = function(e) {
			const code = $(this).data('lang')
			const post_name = $(`#lang-${code}`).val()
			$(`#text-edit-${code}`).text( post_name );

			$(`#lang-${code}`).hide();
			$(this).hide()
			$(`.button-weglot-lang[data-lang=${code}]`).show()

			$.ajax({
				url: ajaxurl,
				method: "POST",
				data: {
					action: "weglot_post_name",
					lang: code,
					id: $("#weglot_post_id").data('id'),
					post_name: post_name
				},
				success: function(res) {
					if(res.data && res.data.code && res.data.code === 'same_post_name'){
						$(`#text-edit-${code}`).text(old_text[code]);
						return
					}
					else if (res.data && res.data.code && res.data.code ==='not_available'){
						$(`#weglot_permalink_not_available_${code}`).show();
						setTimeout(() => {
							$(`#weglot_permalink_not_available_${code}`).hide();
						}, 5000);
					}

					$(`#text-edit-${code}`).text(res.data.result.slug);
				}
			});
		}

		$(".button-weglot-lang").each((key, itm) => {
			$(itm).on('click', function (e) {
				e.preventDefault()

				const code = $(this).data('lang')
				const text = $(`#text-edit-${code}`).text();
				old_text[code] = text

				$(`#text-edit-${code}`).text(' ');
				$(`#lang-${code}`).val(text).show();
				$(`.button-weglot-lang-submit[data-lang=${code}]`).show();
				$(this).hide()
			})

			const code = $(itm).data('lang')

			$(`.button-weglot-lang-submit[data-lang=${code}]`)
				.on("click", edit_weglot_post_name);
		})
	};

	document.addEventListener("DOMContentLoaded", () => {
		execute();
	});

}

export default init_url_translate
