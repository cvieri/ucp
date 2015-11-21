var CallforwardC = UCPMC.extend({
	init: function() {
	},
	settingsDisplay: function() {
		$("#module-Callforward form .input-group").each(function( index ) {
			$(this).find("input[type=\"text\"]").prop("disabled", !$(this).find("input[type=\"checkbox\"]").is(":checked"));
		});
		$("#cfringtimer").change(function() {
			Callforward.saveSettings({ ringtimer: $(this).val() });
		});
		$("#module-Callforward .cfnumber input[type=\"text\"]").change(function() {
			$(this).blur(function() {
				var el = $(this).prop("name"),
						id = $(this).prop("id");
				if ($(".cfnumber input[data-el=\"" + el + "\"]").is(":checked")) {
					if ($(this).val() !== "") {
						Callforward.saveSettings({ number: $(this).val(), type: $(this).data("type") });
					} else {
						$("#" + id + "_number_enable").click();
						Callforward.saveSettings({ number: "", type: $(this).data("type") });
					}
				}
				$(this).off("blur");
			});
		});
		$("#module-Callforward .cfnumber input[type=\"checkbox\"]").change(function() {
			var el = $(this).data("el");
			if (!$(this).is(":checked")) {
				$("#" + el).prop("disabled", true);
				if ($("#" + el).val() !== "") {
					$("#" + el).val("");
					Callforward.saveSettings({ number: "", type: $("#" + el).data("type") });
				}
			} else {
				$("#" + el).prop("disabled", false);
			}
		});
	},
	settingsHide: function() {
		$("#cfringtimer").off("change");
		$(".cfnumber input[type=\"text\"]").off("change");
		$(".cfnumber input[type=\"checkbox\"]").off("change");
	},
	saveSettings: function(data) {
		data.ext = ext;
		$.post( "index.php?quietmode=1&module=callforward&command=settings", data, function( data ) {
			$("#module-Callforward .message").text(data.message).addClass("alert-" + data.alert).fadeIn("fast", function() {
				$(this).delay(2000).fadeOut("fast", function() {
					$(".masonry-container").packery();
				});
			});
			$(".masonry-container").packery();
		});
	}
}), Callforward = new CallforwardC();
