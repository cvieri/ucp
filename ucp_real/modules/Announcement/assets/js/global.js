var AnnouncementC = UCPMC.extend({
	init: function(UCP) {
		var announcement = this;
		this.announcements = {};
		$(document).bind("staticSettingsFinished", function( event ) {
			if (announcement.staticsettings.enabled) {
				announcement.announcements = announcement.staticsettings.announcements;
			}
		});
	},
	poll: function(data) {
		var announcement = this;
		if (data.enabled) {
			announcement.announcements = data.announcements;
		}
	},
	contactClickInitiateCallTo: function(did) {
		window.location.replace("tel:" + did);
	},
	contactClickInitiateFacetime: function(did) {
		window.location.replace("facetime:" + did);
	},
	contactClickOptions: function(type) {
		if (type != "number" || false) {
			return false;
		}
		var options = [ { text: _("Call To"), function: "contactClickInitiateCallTo", type: "phone" }];
		if (navigator.appVersion.indexOf("Mac")!=-1) {
			options.push({ text: _("Facetime"), function: "contactClickInitiateFacetime", type: "phone" });
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
			if (UCP.validMethod(module, "contactClickOptions")) {
				var o = UCP.Modules[module].contactClickOptions(type), selected = "";
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
				"<select id=\"contactmanageraction\" class=\"form-control\">" + options + "</select><button class=\"btn btn-default\" id=\"initiateaction\" style=\"margin-left: 72px;\">Initiate</button>",
				115,
				250,
				function() {
					$("#initiateaction").click(function() {
						var func = $("#contactmanageraction option:selected").data("function"),
						mod = $("#contactmanageraction option:selected").data("module");
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
            $(".announcement-header th[class!=\"noclick\"]").click( function() {
                    var icon = $(this).children("i"),
                    visible = icon.is(":visible"),
                    direction = icon.hasClass("fa-chevron-down") ? "up" : "down",
                    type = $(this).data("type"),
                    search = (typeof $.url().param("search") !== "undefined") ? "&search=" + $.url().param("search") : "",
                    view = (typeof $.url().param("view") !== "undefined") ? "&view=" + $.url().param("view") : "",
                    id = (typeof $.url().param("id") !== "undefined") ? "&id=" + $.url().param("id") : "",
                    uadd = null;
                    if (!visible) 
                    {
                        $(".cdr-header th i").addClass("hidden");
                        icon.removeClass("hidden");
                    }
                    if (direction == "up")
                    {
                        uadd = "&order=asc&orderby=" + type + search;
                        icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
                    }
                    else 
                    {
                        uadd = "&order=desc&orderby=" + type + search;
                        icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
                    }
                    $(".announcement-header th[class!=\"noclick\"]").off("click");
                    $.pjax({ url: "?display=dashboard&mod=announcement" + uadd + view + id, container: "#dashboard-content" });
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
            
            $("#addannouncement").click(function(e) {
                
                var description = $("#description").val();
                
                if(description=='')
                {
                    alert("Invalid description specified");
                    $("#description").focus();
                    return false;
                }
                
                e.preventDefault();
                var announcement = { entrydest: [], entryext: [], entryannouncementret: [] }, $this = this;
                
//                if ($('#invalid_loops').val() != 'disabled')
//                {
//                    var invalid = $('[name=' + $('[name=gotoinvalid]').val() + 'invalid]').val();
//                    $('#invalid_destination').val(invalid);
//                }
//                else
//                {
//                    $('#invalid_destination').remove();
//                }
//                
//                if ($('#timeout_loops').val() != 'disabled')
//                {
//                    var timeout = $('[name=' + $('[name=gototimeout]').val() + 'timeout]').val();
//                    $('#timeout_destination').val(timeout);
//                }
//                else
//                {
//                    $('#timeout_destination').remove();
//                }
                                
                $("form input,select").not(".special").each(function(i, v) {
                    var item = $(v);
                    if(item.prop("id")=='retvm')
                    {
                        var revtm = '';
                        if($("#retvm").is(":checked"))
                        {
                            revtm = 'on';
                        }
                        announcement[item.prop("id")] = revtm;
                    }
                    else if(item.prop("id")=='allow_skip' || item.prop("id")=='return_ivr' || item.prop("id")=='noanswer')
                    {
                        var itemvalue = '0';
                        if($("#"+item.prop("id")).is(":checked"))
                        {
                            itemvalue = '1';
                        }
                        announcement[item.prop("id")] = itemvalue;
                    }
                    else
                    {
                        announcement[item.prop("id")] = item.val();
                    }
                });
                
                $('.entryext').each(function (){
                    announcement.entryext.push($(this).val());
                });
                $('[name^=goto]').each(function(){
                    num = $(this).attr('name').replace('goto', '');
                    if(num!='invalid' && num!='timeout')
                    {
                        dest = $('[name=' + $(this).val() + num + ']').val();
                        announcement.entrydest.push(dest);
                    }
		});
                $('.entryannouncementret').each(function (){
                    if($(this).is(":checked"))
                    {
                        entryannouncementret = '1';
                    }
                    else
                    {
                        entryannouncementret = '0';
                    }
                    announcement.entryannouncementret.push(entryannouncementret);
                });
                
                $('.destdropdown, .destdropdown2').attr("disabled", "disabled");

                $("form input").prop("disabled", true);
                $(this).text(_("Adding..."));
                                
                $(this).prop("disabled", true);
                $.post( "?quietmode=1&module=announcement&command=addannouncement", { announcement: announcement }, function( data ) {
                    if (data.status) {
                        $.pjax({
                                url: "?display=dashboard&mod=announcement",
                                container: "#dashboard-content"
                        });
                    } else {
                        $($this).prop("disabled", false);
                    }
                });
            });
            
            
            $("#editannouncement").click(function(e) {
                
                var description = $("#description").val();
                if(description=='')
                {
                    alert("Invalid description specified");
                    $("#description").focus();
                    return false;
                }
                
                e.preventDefault();
                var announcement = { entrydest: [], entryext: [], entryannouncementret: [] }, $this = this,  id = $("#id").val();
                
//                if ($('#invalid_loops').val() != 'disabled')
//                {
//                    var invalid = $('[name=' + $('[name=gotoinvalid]').val() + 'invalid]').val();
//                    $('#invalid_destination').val(invalid);
//                }
//                else
//                {
//                    $('#invalid_destination').remove();
//                }
//                
//                if ($('#timeout_loops').val() != 'disabled')
//                {
//                    var timeout = $('[name=' + $('[name=gototimeout]').val() + 'timeout]').val();
//                    $('#timeout_destination').val(timeout);
//                }
//                else
//                {
//                    $('#timeout_destination').remove();
//                }

                //var FormData = $("#editIvr").serialize();
                $("form input,select").not(".special").each(function(i, v) {
                    var item = $(v);
                    if(item.prop("id")=='retvm')
                    {
                        var revtm = '';
                        if($("#retvm").is(":checked"))
                        {
                            revtm = 'on';
                        }
                        announcement[item.prop("id")] = revtm;
                    }
                    else if(item.prop("id")=='allow_skip' || item.prop("id")=='return_ivr' || item.prop("id")=='noanswer')
                    {
                        var itemvalue = '0';
                        if($("#"+item.prop("id")).is(":checked"))
                        {
                            itemvalue = '1';
                        }
                        announcement[item.prop("id")] = itemvalue;
                    }
                    else
                    {
                        announcement[item.prop("id")] = item.val();
                    }
                });
                
                
                $('.entryext').each(function (){
                    announcement.entryext.push($(this).val());
                });
                $('[name^=goto]').each(function(){
                    num = $(this).attr('name').replace('goto', '');
                    if(num!='invalid' && num!='timeout')
                    {
                        dest = $('[name=' + $(this).val() + num + ']').val();
                        announcement.entrydest.push(dest);
                    }
		});
                $('.entryannouncementret').each(function (){
                    if($(this).is(":checked"))
                    {
                        entryannouncementret = '1';
                    }
                    else
                    {
                        entryannouncementret = '0';
                    }
                    announcement.entryannouncementret.push(entryannouncementret);
                });
                
                $('.destdropdown, .destdropdown2').attr("disabled", "disabled");
                
                $("form input").prop("disabled", true);
                $(this).text(_("Updating..."));

                $(this).prop("disabled", true);
                $.post( "?quietmode=1&module=announcement&command=updateannouncement", { id: id, announcement: announcement }, function( data ) {
                    if (data.status) {
                        $.pjax({
                                url: "?display=dashboard&mod=announcement",
                                container: "#dashboard-content"
                        });
                    } else {
                        $($this).prop("disabled", false);
                    }
                });
            });
            
            
            
            /*$("#gotoinvalid").change(function(e) {
                var gotoinvalid = $("#gotoinvalid").val();
                if(gotoinvalid=='')
                {
                    $('.invaliddestination').css("display","none");
                }
                else
                {
                    $('.invaliddestination').css("display","none");
                    $('#'+gotoinvalid+"invalid").css("display","block");
                }
            });
            $("#gototimeout").change(function(e) {
                var gototimeout = $("#gototimeout").val();
                if(gototimeout=='')
                {
                    $('.timeoutdestination').css("display","none");
                }
                else
                {
                    $('.timeoutdestination').css("display","none");
                    $('#'+gototimeout+"timeout").css("display","block");
                }
                
            });*/
            
            bind_dests_double_selects();
            
            $('#add_entrie').click(function(){
		// we get this each time in case a popOver has updated the array
		new_entrie = '<tr>' + $('#gotoDESTID').parents('tr').html() + '</tr>';
		id = new Date().getTime();//must be cached, as we have many replaces to do and the time can shift
		thisrow = $('#announcement_entries > tbody:last').find('tr:last').after(new_entrie.replace(/DESTID/g, id));
		$('.destdropdown2', $(thisrow).next()).hide();
		bind_dests_double_selects();
            });
                
	    
            $("#deleteannouncement").click(function(e) {
                e.preventDefault();
                var id = $("#id").val();
                if (confirm(_("Are you sure you want to delete this announcement?"))) {
                    $("form input").prop("disabled", true);
                    $("#deleteannouncement").text(_("Deleting..."));
                    $("#deleteannouncement").prop("disabled", true);
                    $.post( "?quietmode=1&module=announcement&command=deleteannouncement", { id: id }, function( data ) {
                            if (data.status) {
                                    $.pjax({
                                            url: "?display=dashboard&mod=announcement",
                                            container: "#dashboard-content"
                                    });
                            }
                    });
                }
            });
            
            $(".announcement-item").click(function() {
                $.pjax({
                    url: "?display=dashboard&mod=announcement&view=editannouncement&id=" + $(this).data("announcement") + "&mode=edit",
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
            
            
            invalid_elements();
            timeout_elements();

            //show/hide invalid elements on change
            $('#invalid_loops').change(invalid_elements);

            //show/hide timeout elements on change
            $('#timeout_loops').change(timeout_elements);
            
	},
	search: function(text) {
            var view = (typeof $.url().param("view") !== "undefined") ? "&view=" + $.url().param("view") : "",
                id = (typeof $.url().param("id") !== "undefined") ? "&id=" + $.url().param("id") : "";
            if (text !== "") {
                $.pjax({
                    url: "?display=dashboard&mod=announcement&search=" + encodeURIComponent(text) + view + id,
                    container: "#dashboard-content"
                });
            } else {
                $.pjax({
                        url: "?display=dashboard&mod=announcement" + view + id,
                        container: "#dashboard-content"
                });
            }
	},
	hide: function(event) {
		$(".clickable").off("click");
		$(".contact-item").off("click");
		$("#deleteannouncement").off("click");
		$("#editAnnouncement input").off("blur");
		$("#addannouncement, #updateannouncement").off("click");
		$("#editannouncement");
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


function invalid_elements()
{    
    var invalid_elements = $('#invalid_retry_recording, #invalid_recording, #invalid_append_announce, #invalid_announcement_ret, [name=gotoinvalid]');
    var invalid_element_tr = invalid_elements.parent();
    switch ($('#invalid_loops').val())
    {
        case 'disabled':
            invalid_elements.attr('disabled', 'disabled')
            invalid_element_tr.hide();
            $('.invaliddestination').css("display","none");
        break;
        case '0':
            invalid_elements.removeAttr('disabled')
            invalid_element_tr.show();
            $('#invalid_retry_recording').parent().hide();
            $('#invalid_append_announce').parent().hide();
            var gotoinvalid = $("#gotoinvalid").val();
            $('#'+gotoinvalid+"invalid").css("display","block");
        break;
        default:
            invalid_elements.removeAttr('disabled')
            invalid_element_tr.show()
        break;
    }
}

//always disable hidden elements so that they dont trigger validation
function timeout_elements()
{
    var timeout_elements = $('#timeout_retry_recording, #timeout_recording, #timeout_append_announce, #timeout_announcement_ret, [name=gototimeout]');
    var timeout_element_tr = timeout_elements.parent();
    switch ($('#timeout_loops').val())
    {
        case 'disabled':
            timeout_elements.attr('disabled', 'disabled')
            timeout_element_tr.hide()
            $('.timeoutdestination').css("display","none");
        break;
        case '0':
            timeout_elements.removeAttr('disabled')
            timeout_element_tr.show();
            $('#timeout_retry_recording').parent().hide();
            $('#timeout_append_announce').parent().hide();
            var gototimeout = $("#gototimeout").val();
            $('#'+gototimeout+"timeout").css("display","block");
        default:
            timeout_elements.removeAttr('disabled')
            timeout_element_tr.show()
        break;
    }
}

function bind_dests_double_selects()
{
    $(".destdropdown").unbind().bind("change", function(e) {
        var id = $(this).data("id"), dest	= $(this).val();
        id = (typeof id == "undefined") ? "" : id; //ensure id isn't set to undefined

        $("[data-id=" + id + "].destdropdown2").hide();
        dd2 = $("#" + dest + id + ".destdropdown2");
        cur_val = dd2.show().val();

        // This was added because a cancel can leave dd2 cur_val to popover
        // even when there are other choices so we force it to 'none'
        if (dd2.children().length > 1 && cur_val == "popover") {
                dd2.val("");
                cur_val = "";
        }
        if (cur_val == "popover") {
                dd2.trigger("change");
        }
    });
}