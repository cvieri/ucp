var InboundsettingsC = UCPMC.extend({
	init: function(UCP) {
		var cm = this;
		this.dids = {};
		$(document).bind("staticSettingsFinished", function( event ) {
			if (cm.staticsettings.enabled) {
				cm.dids = cm.staticsettings.dids;
			}
		});
	},
	poll: function(data) {
		var cm = this;
		if (data.enabled) {
			cm.dids = data.dids;
		}
	},
	didClickInitiateCallTo: function(did) {
		window.location.replace("tel:" + did);
	},
	didClickInitiateFacetime: function(did) {
		window.location.replace("facetime:" + did);
	},
	didClickOptions: function(type) {
		if (type != "number" || false) {
			return false;
		}
		var options = [ { text: _("Call To"), function: "didClickInitiateCallTo", type: "phone" }];
		if (navigator.appVersion.indexOf("Mac")!=-1) {
			options.push({ text: _("Facetime"), function: "didClickInitiateFacetime", type: "phone" });
		}
		return options;
	},
	showActionDialog: function(type, text, p) {
		var options = "", count = 0, operation = [], primary = "";
		if (typeof type === "undefined" || typeof text === "undefined" ) {
			return;
		}

		primary = (typeof p !== "undefined") ? p : "";
		if(primary.indexOf(",") !=-1) {
			var primaries = primary.split(",");
		}
		if (type == "number") {
			text = text.replace(/\D/g, "");
		}
		$.each(modules, function( index, module ) {
			if (UCP.validMethod(module, "didClickOptions")) {
				var o = UCP.Modules[module].didClickOptions(type), selected = "";
				if (o !== false && Array.isArray(o)) {
					$.each(o, function(k, v) {
						if(typeof primaries !== "undefined") {
							if (primaries.indexOf(v.type) !=-1) {
								if(primaries.indexOf(v.type) === 0) {
									options = "<option data-function='" + v.function + "' data-module='" + module + "' " + selected + ">" + v.text + "</option>" + options;
								} else {
									options = options + "<option data-function='" + v.function + "' data-module='" + module + "' " + selected + ">" + v.text + "</option>";
								}
								v.module = module;
								operation = v;
								count++;
							}
						} else {
							if ((typeof v.type !== "undefined") && (v.type == primary)) {
								options = "<option data-function='" + v.function + "' data-module='" + module + "' " + selected + ">" + v.text + "</option>" + options;
								v.module = module;
								operation = v;
								count++;
							}
						}
					});
				}
			}
		});

		if (count === 0) {
			alert(_("There are no actions for this type"));
		} else if (count === 1) {
			if (UCP.validMethod(operation.module, operation.function)) {
				UCP.Modules[operation.module][operation.function](text);
			}
		} else if (count > 1) {
			UCP.showDialog(_("Select an Action"),
				"<select id=\"didmanageraction\" class=\"form-control\">" + options + "</select><button class=\"btn btn-default\" id=\"initiateaction\" style=\"margin-left: 72px;\">Initiate</button>",
				115,
				250,
				function() {
					$("#initiateaction").click(function() {
						var func = $("#didmanageraction option:selected").data("function"),
						mod = $("#didmanageraction option:selected").data("module");
						if (UCP.validMethod(mod, func)) {
							UCP.closeDialog(function() {
								UCP.Modules[mod][func](text);
							});
						} else {
							alert(_("Function call does not exist!"));
						}
					});
				}
			);
		}
	},
	display: function(event) {
		var $this = this;
		$(".clickable").click(function(e) {
			var type = $(this).data("type"),
					text = $(this).text(),
					primary = $(this).data("primary");
			$this.showActionDialog(type, text, primary);
		});
		$(".did-header th[class!=\"noclick\"]").click( function() {
			var icon = $(this).children("i"),
			visible = icon.is(":visible"),
			direction = icon.hasClass("fa-chevron-down") ? "up" : "down",
			type = $(this).data("type"),
			search = (typeof $.url().param("search") !== "undefined") ? "&search=" + $.url().param("search") : "",
			view = (typeof $.url().param("view") !== "undefined") ? "&view=" + $.url().param("view") : "",
			id = (typeof $.url().param("id") !== "undefined") ? "&id=" + $.url().param("id") : "",
			uadd = null;
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
			$(".did-header th[class!=\"noclick\"]").off("click");
			$.pjax({ url: "?display=dashboard&mod=inboundsettings" + uadd + view + id, container: "#dashboard-content" });
		});
		$("#search-text").keypress(function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if (code == 13) {
				$this.search($(this).val());
				e.preventDefault();
			}
		});
		$("#search-btn").click(function() {
			$this.search($("#search-text").val());
		});
		
		
		$("#adddidbutton").click(function(e) {
                    e.preventDefault();
                    var did = { entrydest: [], entryext: [], entryivrret: [] }, $this = this,  id = $("#id").val();

                    $("form input,select").not(".special").each(function(i, v) {
                        var item = $(v);
                        did[item.prop("id")] = item.val();                        
                    });                    	
                    
                    $("form input").prop("disabled", true);
                    $(this).text(_("Adding..."));
                    $(this).prop("disabled", true);
                    
                    $.post( "?quietmode=1&module=inboundsettings&command=adddid", { id: id, did: did }, function( data ) {
                        if (data.status) 
                        {
                            $.pjax({
                                url: "?display=dashboard&mod=inboundsettings",
                                container: "#dashboard-content"
                            });
                        } else {
                                $($this).prop("disabled", false);
                        }
                    });
		});
                
                $("#editdidbutton").click(function(e) {
                   
                    if($('#ivr').val().trim()=='FORWARD' && $('#forwardnumber').val().trim()=='') {
                        alert('Please enter the Forwarding Number.');
                        return false;
                    }
                    
                    e.preventDefault();
                    var did = { entrydest: [], entryext: [], entryivrret: [] }, $this = this,  id = $("#id").val();

                    $("form input,select").not(".special").each(function(i, v) {
                        var item = $(v);
                        did[item.prop("id")] = item.val();                        
                    });
                
                    $("form input").prop("disabled", true);
                    $(this).text(_("Updating..."));

                    $(this).prop("disabled", true);
                    $.post( "?quietmode=1&module=inboundsettings&command=updatedid", { id: id, did: did }, function( data ) {
                        if (data.status) {
                            $.pjax({
                                    url: "?display=dashboard&mod=inboundsettings",
                                    container: "#dashboard-content"
                            });
                        } else {
                            $($this).prop("disabled", false);
                        }
                    });
                });
		
		$("#deletedid").click(function(e) {
                    e.preventDefault();
                    var id = $("#id").val();
                    if (confirm(_("Are you sure you want to delete this DID?")))
                    {
                        $("form input").prop("disabled", true);
                        $("#deletedid").text(_("Deleting..."));
                        $("#deletedid").prop("disabled", true);
                        $.post( "?quietmode=1&module=inboundsettings&command=deletedid", { id: id }, function( data ) {
                            if (data.status) {
                                $.pjax({
                                    url: "?display=dashboard&mod=inboundsettings",
                                    container: "#dashboard-content"
                                });
                            }
                        });
                    }
		});
				
		
                $(".did-item").click(function() {
                    $.pjax({
                        url: "?display=dashboard&mod=inboundsettings&view=editdid&id=" + $(this).data("did") + "&mode=edit",
                        container: "#dashboard-content"
                    });
                });
                
		//clear old binds
		$(document).off("click", "[cm-pjax] a, a[cm-pjax], [vm-pjax] a, a[vm-pjax]");
		//then rebind!
		if ($.support.pjax) {
			$(document).on("click", "[cm-pjax] a, a[cm-pjax], [vm-pjax] a, a[vm-pjax]", function(event) {
				var container = $("#dashboard-content");
				$.pjax.click(event, { container: container });
			});
		}
	},
	search: function(text) {
		var view = (typeof $.url().param("view") !== "undefined") ? "&view=" + $.url().param("view") : "",
				id = (typeof $.url().param("id") !== "undefined") ? "&id=" + $.url().param("id") : "";
		if (text !== "") {
			$.pjax({
				url: "?display=dashboard&mod=inboundsettings&search=" + encodeURIComponent(text) + view + id,
				container: "#dashboard-content"
			});
		} else {
			$.pjax({
				url: "?display=dashboard&mod=inboundsettings" + view + id,
				container: "#dashboard-content"
			});
		}
	},
	hide: function(event) {
		$(".clickable").off("click");
		$(".contact-item").off("click");
		$("#addgroup").off("click");
		$("#deletegroup").off("click");
		$("#deletecontact").off("click");
		$("#editContact input").off("blur");
		$("#addcontact, #updatecontact").off("click");
		$("#editcontact");
	},
	/**
	 * Lookup a contact from the directory
	 * @param  {string} search The string to look for
	 * @param  {object} regExp The regular expression object (make sure /g is on the end)
	 * @return {string} replaced value
	 */
	lookup: function(search, regExp) {
		var o = this.recursiveObjectSearch(search, this.contacts), contact;
		if (o !== false) {
			contact = this.contacts[o[0]];
			if (contact !== false) {
				contact.ignore = o[0];
				contact.key = o[o.length - 1];
			}
			return contact;
		}
		return false;
	},
	recursiveObjectSearch: function(search, haystack, key, strict, stack) {
		var k, o, pattern = new RegExp(search);
		for (k in haystack) {
			if (haystack.hasOwnProperty(k) && haystack[k] !== null) {
				if (typeof stack === "undefined") {
					stack = [];
				}
				if (typeof haystack[k] === "object") {
					stack.push(k);
					o = this.recursiveObjectSearch(search, haystack[k], key, strict, stack);
					if (o !== false) {
						return stack;
					} else {
						stack = [];
					}
				} else if (pattern.test(haystack[k])) {
					stack.push(k);
					return stack;
				}
			}
		}
		return false;
	}
});
