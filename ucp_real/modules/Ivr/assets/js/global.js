var IvrC = UCPMC.extend({
	init: function(UCP) {
		var ivr = this;
		this.ivrs = {};
		$(document).bind("staticSettingsFinished", function( event ) {
			if (ivr.staticsettings.enabled) {
				ivr.ivrs = ivr.staticsettings.ivrs;
			}
		});
	},
        settingsDisplay: function() {
		$('#useivr').change(function() {
			$.post( "index.php?quietmode=1&module=ivr&command=enable", {enable: $(this).is(':checked'), ext: ext}, function( data ) {
				$('#module-Ivr .message').text(data.message).addClass('alert-'+data.alert).fadeIn('fast', function() {
					$(this).delay(2000).fadeOut('fast', function() {
						$('.masonry-container').packery();
					});
				});
				$('.masonry-container').packery();
			});
		});
	},
	settingsHide: function() {
		$('#useivr').off('change');
	},
	poll: function(data) {
		var ivr = this;
		if (data.enabled) {
			ivr.ivrs = data.ivrs;
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
            $(".ivr-header th[class!=\"noclick\"]").click( function() {
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
                    $(".ivr-header th[class!=\"noclick\"]").off("click");
                    $.pjax({ url: "?display=dashboard&mod=ivr" + uadd + view + id, container: "#dashboard-content" });
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
            
            $("#addivr").click(function(e) {
                
                var gotoinvalid = $("#gotoinvalid").val();
                var gototimeout = $("#gototimeout").val();
                if(gotoinvalid=='' && $('#invalid_loops').val() != 'disabled')
                {
                    alert("Please select invalid destination");
                    $("#gotoinvalid").focus();
                    return false;
                }
                if(gototimeout=='' && $('#timeout_loops').val() != 'disabled')
                {
                    alert("Please select timeout destination");
                    $("#gototimeout").focus();
                    return false;
                }
                
                e.preventDefault();
                var ivr = { entrydest: [], entryext: [], entryivrret: [] }, $this = this;
                
                if ($('#invalid_loops').val() != 'disabled')
                {
                    var invalid = $('[name=' + $('[name=gotoinvalid]').val() + 'invalid]').val();
                    $('#invalid_destination').val(invalid);
                }
                else
                {
                    $('#invalid_destination').remove();
                }
                
                if ($('#timeout_loops').val() != 'disabled')
                {
                    var timeout = $('[name=' + $('[name=gototimeout]').val() + 'timeout]').val();
                    $('#timeout_destination').val(timeout);
                }
                else
                {
                    $('#timeout_destination').remove();
                }
                                
                $("form input,select").not(".special").each(function(i, v) {
                    var item = $(v);
                    if(item.prop("id")=='retvm')
                    {
                        var revtm = '';
                        if($("#retvm").is(":checked"))
                        {
                            revtm = 'on';
                        }
                        ivr[item.prop("id")] = revtm;
                    }
                    else if(item.prop("id")=='invalid_append_announce' || item.prop("id")=='invalid_ivr_ret' || item.prop("id")=='timeout_append_announce' || item.prop("id")=='timeout_ivr_ret' || item.prop("id")=='useivr')
                    {
                        var itemvalue = '0';
                        if($("#"+item.prop("id")).is(":checked"))
                        {
                            itemvalue = '1';
                        }
                        ivr[item.prop("id")] = itemvalue;
                    }
                    else
                    {
                        ivr[item.prop("id")] = item.val();
                    }
                });
                
                $('.entryext').each(function (){
                    ivr.entryext.push($(this).val());
                });
                $('[name^=goto]').each(function(){
                    num = $(this).attr('name').replace('goto', '');
                    if(num!='invalid' && num!='timeout')
                    {
                        dest = $('[name=' + $(this).val() + num + ']').val();
                        ivr.entrydest.push(dest);
                    }
		});
                $('.entryivrret').each(function (){
                    if($(this).is(":checked"))
                    {
                        entryivrret = '1';
                    }
                    else
                    {
                        entryivrret = '0';
                    }
                    ivr.entryivrret.push(entryivrret);
                });
                
                $('.destdropdown, .destdropdown2').attr("disabled", "disabled");

                $("form input").prop("disabled", true);
                $(this).text(_("Adding..."));
                                
                $(this).prop("disabled", true);
                $.post( "?quietmode=1&module=ivr&command=addivr", { ivr: ivr }, function( data ) {
                    if (data.status) {
                        $.pjax({
                                url: "?display=dashboard&mod=ivr",
                                container: "#dashboard-content"
                        });
                    } else {
                        $($this).prop("disabled", false);
                    }
                });
            });
            
            
            $("#editivr").click(function(e) {
                
                var gotoinvalid = $("#gotoinvalid").val();
                var gototimeout = $("#gototimeout").val();
                if(gotoinvalid=='' && $('#invalid_loops').val() != 'disabled')
                {
                    alert("Please select invalid destination");
                    $("#gotoinvalid").focus();
                    return false;
                }
                if(gototimeout=='' && $('#timeout_loops').val() != 'disabled')
                {
                    alert("Please select timeout destination");
                    $("#gototimeout").focus();
                    return false;
                }
                
                e.preventDefault();
                var ivr = { entrydest: [], entryext: [], entryivrret: [] }, $this = this,  id = $("#id").val();
                
                if ($('#invalid_loops').val() != 'disabled')
                {
                    var invalid = $('[name=' + $('[name=gotoinvalid]').val() + 'invalid]').val();
                    $('#invalid_destination').val(invalid);
                }
                else
                {
                    $('#invalid_destination').remove();
                }
                
                if ($('#timeout_loops').val() != 'disabled')
                {
                    var timeout = $('[name=' + $('[name=gototimeout]').val() + 'timeout]').val();
                    $('#timeout_destination').val(timeout);
                }
                else
                {
                    $('#timeout_destination').remove();
                }

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
                        ivr[item.prop("id")] = revtm;
                    }
                    else if(item.prop("id")=='invalid_append_announce' || item.prop("id")=='invalid_ivr_ret' || item.prop("id")=='timeout_append_announce' || item.prop("id")=='timeout_ivr_ret' || item.prop("id")=='useivr')
                    {
                        var itemvalue = '0';
                        if($("#"+item.prop("id")).is(":checked"))
                        {
                            itemvalue = '1';
                        }
                        ivr[item.prop("id")] = itemvalue;
                    }
                    else
                    {
                        ivr[item.prop("id")] = item.val();
                    }
                });
                
                
                $('.entryext').each(function (){
                    ivr.entryext.push($(this).val());
                });
                $('[name^=goto]').each(function(){
                    num = $(this).attr('name').replace('goto', '');
                    if(num!='invalid' && num!='timeout')
                    {
                        dest = $('[name=' + $(this).val() + num + ']').val();
                        ivr.entrydest.push(dest);
                    }
		});
                $('.entryivrret').each(function (){
                    if($(this).is(":checked"))
                    {
                        entryivrret = '1';
                    }
                    else
                    {
                        entryivrret = '0';
                    }
                    ivr.entryivrret.push(entryivrret);
                });
                
                $('.destdropdown, .destdropdown2').attr("disabled", "disabled");

                $("form input").prop("disabled", true);
                $(this).text(_("Updating..."));

                $(this).prop("disabled", true);
                $.post( "?quietmode=1&module=ivr&command=updateivr", { id: id, ivr: ivr }, function( data ) {
                    if (data.status) {
                        $.pjax({
                                url: "?display=dashboard&mod=ivr",
                                container: "#dashboard-content"
                        });
                    } else {
                        $($this).prop("disabled", false);
                    }
                });
            });
            
            $("#deleteivr").click(function(e) {
                e.preventDefault();
                var id = $("#id").val();
                if (confirm(_("Are you sure you want to delete this IVR?"))) {
                    $("form input").prop("disabled", true);
                    $("#deleteivr").text(_("Deleting..."));
                    $("#deleteivr").prop("disabled", true);
                    $.post( "?quietmode=1&module=ivr&command=deleteivr", { id: id }, function( data ) {
                            if (data.status) {
                                    $.pjax({
                                            url: "?display=dashboard&mod=ivr",
                                            container: "#dashboard-content"
                                    });
                            }
                    });
                }
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
		thisrow = $('#ivr_entries > tbody:last').find('tr:last').after(new_entrie.replace(/DESTID/g, id));
		$('.destdropdown2', $(thisrow).next()).hide();
		bind_dests_double_selects();
            });
                
            $("#ivr_entries").on("click",".delete_entrie", function(){
                $(this).closest('tr').fadeOut('normal', function(){$(this).closest('tr').remove();})
            });
            
            
                        

            
		
            $(".ivr-item").click(function() {
                $.pjax({
                    url: "?display=dashboard&mod=ivr&view=editivr&id=" + $(this).data("ivr") + "&mode=edit",
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
                    url: "?display=dashboard&mod=ivr&search=" + encodeURIComponent(text) + view + id,
                    container: "#dashboard-content"
                });
            } else {
                $.pjax({
                        url: "?display=dashboard&mod=ivr" + view + id,
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


function invalid_elements()
{    
    var invalid_elements = $('#invalid_retry_recording, #invalid_recording, #invalid_append_announce, #invalid_ivr_ret, [name=gotoinvalid]');
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
    var timeout_elements = $('#timeout_retry_recording, #timeout_recording, #timeout_append_announce, #timeout_ivr_ret, [name=gototimeout]');
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