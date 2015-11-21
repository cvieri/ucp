var SmsC = UCPMC.extend({
	init: function(UCP) {
		this.packery = false;
		this.doit = null;
		this.lastchecked = Math.round(new Date().getTime() / 1000);
		this.dids = [];
		this.icon = "fa fa-comments-o";
		//Logged In
		var Sms = this;
		$(document).bind("logIn", function( event ) {
			$("#sms-menu a.new").on("click", function() {
				var sfrom = "";
				$.each(Sms.dids, function(i, v) {
					sfrom = sfrom + "<option>" + v + "</option>";
				});
				UCP.showDialog(_("Send Message"),
					"<label for=\"SMSfrom\">From:</label> <select id=\"SMSfrom\" class=\"form-control\">" + sfrom + "</select><label for=\"SMSto\">To:</label><select class=\"form-control Tokenize Fill\" id=\"SMSto\" multiple></select><button class=\"btn btn-default\" id=\"initiateSMS\" style=\"margin-left: 72px;\">Initiate</button>",
					200,
					250,
					function() {
						$("#SMSto").tokenize({
							maxElements: 1,
							datas: "index.php?quietmode=1&module=sms&command=contacts"
						});
						$("#initiateSMS").click(function() {
							setTimeout(function() {Sms.initiateChat();}, 50);
						});
						$("#SMSto").keypress(function(event) {
							if (event.keyCode == 13) {
								setTimeout(function() {Sms.initiateChat();}, 50);
							}
						});
					}
				);
			});
			$("#sms-menu a.did").on("click", function() {
				var tdid = $(this).data("did"),
						sfrom = "",
						name = tdid,
						selected = "",
						temp = "";
				if (UCP.validMethod("Contactmanager", "lookup")) {
					if (typeof UCP.Modules.Contactmanager.lookup(tdid).displayname !== "undefined") {
						name = UCP.Modules.Contactmanager.lookup(tdid).displayname;
					} else {
						temp = String(tdid).length == 11 ? String(tdid).substring(1) : tdid;
						if (typeof UCP.Modules.Contactmanager.lookup(temp).displayname !== "undefined") {
							name = UCP.Modules.Contactmanager.lookup(temp).displayname;
						}
					}
				}
				selected = "<option value=\"" + tdid + "\" selected>" + name + "</option>";
				$.each(Sms.dids, function(i, v) {
					sfrom = sfrom + "<option>" + v + "</option>";
				});
				UCP.showDialog(_("Send Message"),
					"<label for=\"SMSfrom\">From:</label> <select id=\"SMSfrom\" class=\"form-control\">" + sfrom + "</select><label for=\"SMSto\">To:</label><select class=\"form-control Tokenize Fill\" id=\"SMSto\" multiple>" + selected + "</select><button class=\"btn btn-default\" id=\"initiateSMS\" style=\"margin-left: 72px;\">Initiate</button>",
					200,
					250,
					function() {
						$("#SMSto").tokenize({
							maxElements: 1,
							datas: "index.php?quietmode=1&module=sms&command=contacts"
						});
						$("#initiateSMS").click(function() {
							setTimeout(function() {Sms.initiateChat();}, 50);
						});
						$("#SMSto").keypress(function(event) {
							if (event.keyCode == 13) {
								setTimeout(function() {Sms.initiateChat();}, 50);
							}
						});
					}
				);
			});
		});

		$(document).on("chatWindowAdded", function(event, windowId, module, object) {
			if (module == "Sms") {
				object.on("click", function() {
					object.find(".title-bar").css("background-color", "");
				});
				var from = $(".message-box[data-id=\"" + windowId + "\"]").data("from"),
				to = $(".message-box[data-id=\"" + windowId + "\"]").data("to");
				object.find("textarea").keyup(function(event) {
					if (event.keyCode == 13) {
						var message = $(this).val();
						Sms.sendMessage(windowId, from, to, message);
					}
				});
				object.find(".chat").scroll(function() {
					if ($(this)[0].scrollTop === 0) {
						var id = $(".chat .message:lt(1)").data("id");
						$(".message-box[data-id=\"" + windowId + "\"] .chat .history").prepend('<div class="message status">Loading...</div>');
						$.post( "index.php?quietmode=1&module=sms&command=history", { id: id, from: from, to: to }, function( data ) {
							$(".message-box[data-id=\"" + windowId + "\"] .chat .history .status").remove();
							var html = "";
							$.each(data.messages, function(i, v) {
								html = html + "<div class=\"message\" data-id=\"" + v.id + "\"><strong>" + v.from + ":</strong>" + v.message + "</div>";
							});
							$(".message-box[data-id=\"" + windowId + "\"] .chat .history").prepend(html);
						});
					}
				});
			}
		});

		$(document).bind("staticSettingsFinished", function( event ) {
			if ((typeof Sms.staticsettings !== "undefined") && Sms.staticsettings.enabled) {
				Sms.dids = Sms.staticsettings.dids;
			}
		});
	},
	contactClickInitiate: function(did) {
		var tdid = did, Sms = this,
		sfrom = "",
		name = tdid,
		selected = "",
		temp = "";
		if (UCP.validMethod("Contactmanager", "lookup")) {
			if (typeof UCP.Modules.Contactmanager.lookup(tdid).displayname !== "undefined") {
				name = UCP.Modules.Contactmanager.lookup(tdid).displayname;
			} else {
				temp = String(tdid).length == 11 ? String(tdid).substring(1) : tdid;
				if (typeof UCP.Modules.Contactmanager.lookup(temp).displayname !== "undefined") {
					name = UCP.Modules.Contactmanager.lookup(temp).displayname;
				}
			}
		}

		selected = "<option value=\"" + tdid + "\" selected>" + name + "</option>";
		$.each(Sms.dids, function(i, v) {
			sfrom = sfrom + "<option>" + v + "</option>";
		});
		UCP.showDialog(_("Send Message"),
			"<label for=\"SMSfrom\">From:</label> <select id=\"SMSfrom\" class=\"form-control\">" + sfrom + "</select><label for=\"SMSto\">To:</label><select class=\"form-control Tokenize Fill\" id=\"SMSto\" multiple>" + selected + "</select><button class=\"btn btn-default\" id=\"initiateSMS\" style=\"margin-left: 72px;\">Initiate</button>",
			200,
			250,
			function() {
				$("#SMSto").tokenize({
					maxElements: 1,
					datas: "index.php?quietmode=1&module=sms&command=contacts"
				});
				$("#initiateSMS").click(function() {
					setTimeout(function() {Sms.initiateChat();}, 50);
				});
				$("#SMSto").keypress(function(event) {
					if (event.keyCode == 13) {
						setTimeout(function() {Sms.initiateChat();}, 50);
					}
				});
			}
		);
	},
	contactClickOptions: function(type) {
		if (type != "number") {
			return false;
		}
		return [ { text: _("Send SMS"), function: "contactClickInitiate", type: "sms" } ];
	},
	replaceContact: function(contact) {
		var entry = null;
		if (UCP.validMethod("Contactmanager", "lookup")) {
			scontact = contact.length == 11 ? contact.substring(1) : contact;
			entry = UCP.Modules.Contactmanager.lookup(scontact);
			if (entry !== null && entry !== false) {
				return entry.displayname;
			}
			entry = UCP.Modules.Contactmanager.lookup(contact);
			if (entry !== null && entry !== false) {
				return entry.displayname;
			}
		}
		return contact;
	},
	prepoll: function(data) {
		var Sms = this,
				messageBoxes = { messageWindows: {}, lastchecked: this.lastchecked };
		$(".message-box[data-module=\"Sms\"]").each(function(i, v) {
			var windowid = $(this).data("id"),
					from = $(this).data("from"),
					to = $(this).data("to"),
					last = $(this).data("last-msg-id");
					messageBoxes.messageWindows[i] = { from: from, to: to, last: last, windowid: windowid };
		});
		this.lastchecked = Math.round(new Date().getTime() / 1000);
		return messageBoxes;
	},
	poll: function(data) {
		var Sms = this,
				delivered = [];
		if (data.status) {
			$.each(data.messages, function(windowid, messages) {
				$.each(messages, function(index, v) {
					if (!$(".message-box[data-id=\"" + windowid + "\"] .message[data-id=\"" + v.id + "\"]").length) {
						var Notification = new Notify(sprintf(_("New Message from %s"), Sms.replaceContact(v.from)), {
							body: emojione.unifyUnicode(v.body),
							icon: "modules/Sms/assets/images/comment.png",
							timeout: 3
						});
						UCP.addChat("Sms", windowid, Sms.icon, v.did, v.recp, Sms.replaceContact(v.cnam), v.id, v.body);
						delivered.push(v.id);
						if (UCP.notify) {
							Notification.show();
						}
					}
				});
			});
			if (delivered.length) {
				$.post( "index.php?quietmode=1&module=sms&command=delivered", { ids: delivered }, function( data ) {});
			}
		}
	},
	display: function(event) {
		var Sms = this;
		$(document).on("click", "[vm-pjax] a, a[vm-pjax]", function(event) {
			event.preventDefault(); //stop browser event
			var container = $("#dashboard-content");
			$.pjax.click(event, { container: container });
		});
		$(".message-header th[class!=\"noclick\"]").click( function() {
			var icon = $(this).children("i"),
					visible = icon.is(":visible"),
					direction = icon.hasClass("fa-chevron-down") ? "up" : "down",
					type = $(this).data("type"),
					uadd = null,
					search = (typeof $.url().param("search") !== "undefined") ? "&search=" + $.url().param("search") : "";
			if (!visible) {
				$(".cdr-header th i").addClass("hidden");
				icon.removeClass("hidden");
			}
			if (direction == "up") {
				uadd = "&order=asc&orderby=" + type + search;
				icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
			} else {
				uadd = "&order=desc&orderby=" + type + search;
				icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
			}
			$(".cdr-header th[class!=\"noclick\"]").off("click");
			$.pjax({ url: "?display=dashboard&mod=sms" + uadd, container: "#dashboard-content" });
		});
		$("i.fa-eye").click(function() {
			var id = $(this).data("id");
			$("#" + id + "-messages").toggle();
			$("#" + id + "-messages").find(".sms-message-body").each(function() { $(this).scrollTop(0); });
		});
		$("i.fa-trash-o").click(function() {
			if (confirm(_("Are you Sure you wish to delete this chat history?"))) {
				var thread = $(this).parents(".sms-message");
				$.post( "index.php?quietmode=1&module=sms&command=delete", { from: thread.data("from"), to: thread.data("to") }, function( data ) {
					if (data.status) {
						thread.fadeOut("slow");
					} else {

					}
				});
			}
		});
		$("#search-text").keypress(function(e) {
			var code = null;
			code = (e.keyCode ? e.keyCode : e.which);
			if (code == 13) {
				Sms.search($(this).val());
				e.preventDefault();
			}
		});
		$("#search-btn").click(function() {
			Sms.search($("#search-text").val());
		});
		if (typeof $.url().param("search") !== "undefined") {
			$(".sms-message-body").highlight($.url().param("search"), "yellow");
		}
	},
	search: function(text) {
		var Sms = this;
		if (text !== "") {
			$.pjax({ url: "?display=dashboard&mod=sms&search=" + encodeURIComponent(text), container: "#dashboard-content" });
		} else {
			$.pjax({ url: "?display=dashboard&mod=sms", container: "#dashboard-content" });
		}
	},
	hide: function(event) {
		var Sms = this;
		$(document).off("click", "[vm-pjax] a, a[vm-pjax]");
	},
	resize: function() {

	},
	initiateChat: function() {
		var Sms = this,
				to = ($("#SMSto").val() !== null) ? $("#SMSto").val()[0] : "",
				from = $("#SMSfrom").val(),
			pattern = new RegExp(/^\d*$/);
		if (to !== "" && to.length <= 11 && to.length >= 10 && pattern.test(to)) {
			to = (to.length === 10) ? "1" + to : to;
			UCP.addChat("Sms", from + to, Sms.icon, from, to);
			UCP.closeDialog();
		} else {
			alert(_("Invalid Number"));
		}
	},
	startChat: function(from, to) {
		var Sms = this;
		UCP.addChat("Sms", from + to, Sms.icon, from, to);
	},
	sendMessage: function(windowId, from, to, message) {
		var Sms = this;
		$(".message-box[data-id='" + windowId + "'] textarea").prop("disabled", true);
		$.post( "index.php?quietmode=1&module=sms&command=send", { from: from, to: to, message: message }, function( data ) {
			if (data.status) {
				UCP.addChatMessage(windowId, from, data.id, message, false);
				$(".message-box[data-id='" + windowId + "'] textarea").val("");
			} else {
				//TODO: Error message about sending here!
				//UCP.addChatMessage(windowId, _("System"), data.id, _("There was an error sending this message: "), false);
			}
			$(".message-box[data-id='" + windowId + "'] textarea").prop("disabled", false);
			$(".message-box[data-id='" + windowId + "'] textarea").focus();
		});
	}
});
