/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 */


Aimeos = {

	options : null,


	init : function() {

	},


	addClone : function(node, getfcn, selectfn, after) {

		var clone = node.clone().removeClass("prototype");
		var combo = $(".combobox-prototype", clone);

		combo.combobox({getfcn: getfcn, select: selectfn});
		combo.removeClass("combobox-prototype");
		combo.addClass("combobox");

		$("[disabled='disabled']", clone).prop("disabled", false);

		if(typeof Modernizr != 'undefined') {
			if(!Modernizr.inputtypes['datetime-local']) {
				$("input[type='datetime-local']", clone).each(function(idx, elem) {
					$(elem).datepicker({
						dateFormat: 'yy-mm-dd',
						constrainInput: false
					});
				});
			}

			if(!Modernizr.inputtypes['date']) {
				$("input[type='date']", clone).each(function(idx, elem) {
					$(elem).datepicker({
						dateFormat: 'yy-mm-dd',
						constrainInput: false
					});
				});
			}
		}

		if(after) {
			clone.insertAfter(node);
		} else {
			clone.insertBefore(node);
		}

		return clone;
	},


	focusBefore : function(node) {

		var elem = $(":focus", node);
		var elements = $(".aimeos [tabindex=" + elem.attr("tabindex") + "]:visible");
		var idx = elements.index(elem) - $("[tabindex=" + elem.attr("tabindex") + "]:visible", node).length;

		if(idx > -1) {
			elements[idx].focus();
		}

		return node;
	},


	getCountries : function(request, response, element) {

		if(request.term.length == 0) {
			var url = 'https://restcountries.eu/rest/v2/all';
		} else if(request.term.length > 1) {
			var url = 'https://restcountries.eu/rest/v2/name/' + request.term;
		} else {
			return;
		}

		$.ajax({
			url: url,
			dataType: "json",
			data: 'fields=alpha2Code;name',
			success: function(result) {
				var list = result || [];

				$("option", element).remove();

				response( list.map(function(obj) {

					var opt = $("<option/>");

					opt.attr("value", obj.alpha2Code);
					opt.text(obj.alpha2Code);
					opt.appendTo(element);

					return {
						label: obj.name || null,
						value: obj.alpha2Code,
						option: opt
					};
				}));
			}
		});

	},


	getOptions : function(request, response, element, domain, key, sort, criteria) {

		Aimeos.options.done(function(data) {

			var compare = {}, field = {}, list = {}, params = {}, param = {};

			compare[key] = request.term;
			list = criteria ? [{'=~': compare}, criteria] : [{'=~': compare}];
			field[domain] = key;

			param['filter'] = {'&&': list};
			param['fields'] = field;
			param['sort'] = sort;

			if( data.meta && data.meta.prefix ) {
				params[data.meta.prefix] = param;
			} else {
				params = param;
			}

			$.ajax({
				dataType: "json",
				url: data.meta.resources[domain] || null,
				data: params,
				success: function(result) {
					var list = result.data || [];

					$("option", element).remove();

					response( list.map(function(obj) {

						var opt = $("<option/>");

						opt.attr("value", obj.id);
						opt.text(obj.attributes[key]);
						opt.appendTo(element);

						return {
							label: obj.attributes[key] || null,
							value: obj.id,
							option: opt
						};
					}));
				}
			});
		});
	},


	getOptionsAttributes : function(request, response, element, criteria) {
		Aimeos.getOptions(request, response, element, 'attribute', 'attribute.label', 'attribute.label', criteria);
	},


	getOptionsCategories : function(request, response, element, criteria) {
		Aimeos.getOptions(request, response, element, 'catalog', 'catalog.label', 'catalog.label', criteria);
	},


	getOptionsCurrencies : function(request, response, element, criteria) {
		Aimeos.getOptions(request, response, element, 'locale/currency', 'locale.currency.id', '-locale.currency.status,locale.currency.id', criteria);
	},


	getOptionsLanguages : function(request, response, element, criteria) {
		Aimeos.getOptions(request, response, element, 'locale/language', 'locale.language.id', '-locale.language.status,locale.language.id', criteria);
	},


	getOptionsSites : function(request, response, element, criteria) {
		Aimeos.getOptions(request, response, element, 'locale/site', 'locale.site.label', '-locale.site.status,locale.site.label', criteria);
	},


	getOptionsProducts : function(request, response, element, criteria) {
		Aimeos.getOptions(request, response, element, 'product', 'product.label', 'product.label', criteria);
	}
};



Aimeos.Config = {

	init : function() {

		this.addConfigLine();
		this.deleteConfigLine();
		this.configComplete();

		this.addConfigMapLine();
		this.deleteConfigMapLine();
		this.hideConfigMap();
		this.showConfigMap();
	},


	setup : function(resource, provider, target, type) {

		if(!provider) {
			return;
		}

		Aimeos.options.done(function(data) {

			if(!data.meta || !data.meta.resources || !data.meta.resources[resource]) {
				return;
			}

			var params = {}, param = {id: provider};

			if(type) {
				param["type"] = type;
			}

			if(data.meta && data.meta.prefix) {
				params[data.meta.prefix] = param;
			} else {
				params = param;
			}

			$.ajax({
				url: data.meta.resources[resource],
				dataType: "json",
				data: params
			}).done(function(result) {

				$(result.data).each(function(idx, entry) {
					var cfgkey = $("table.item-config input.config-key[value='" + entry.id + "']", target);

					if(cfgkey.length > 0) {
						var el = $("table.item-config .config-item.prototype .config-type-" + entry.attributes.type, target).clone();
						var row = cfgkey.closest(".config-item");
						var old = $(".config-type", row);

						$("> [disabled='disabled']", el).prop("disabled", false);
						$("> input", el).val(old.val());
						el.prop("disabled", false);
						el.val(old.val());
						old.remove();

						$(".help-text", row).html(entry.attributes.label);
						$(".config-row-value", row).append(el);
					} else {
						var row = Aimeos.addClone($("table.item-config .config-item.prototype", target));

						$(".config-row-value .config-type:not(.config-type-" + entry.attributes.type + ")", row).remove();
						$(".config-row-key .help-text", row).html(entry.attributes.label);
						$(".config-value", row).val(entry.attributes.default);
						$(".config-key", row).val(entry.id);
					}

					if(!entry.attributes.required) {
						$(".config-value", row).prop("required", false);
						row.removeClass("mandatory");
					} else {
						$(".config-value", row).prop("required", true);
						row.addClass("mandatory");
					}
				});
			});
		});
	},


	addConfigLine : function() {

		$(".aimeos .item .tab-pane").on("click", ".item-config .actions .act-add", function(ev) {

			var node = $(this).closest(".item-config");
			var clone = Aimeos.addClone($(".prototype", node));
			var count = $(".list-item-new", ev.delegateTarget).length - 2; // minus prototype and must start with 0
			var types = $(".config-type", clone);

			if(types.length > 0 ) {
				$(".config-type:not(.config-type-string)", clone).remove();
			}

			$("input", clone).each(function() {
				$(this).attr("name", $(this).attr("name").replace("idx", count));
			});

			$(".config-key", clone).autocomplete({
				source: node.data("keys") || [],
				minLength: 0,
				delay: 0
			});
		});
	},


	deleteConfigLine : function() {

		$(".aimeos .item .tab-pane").on("click", ".item-config .actions .act-delete", function(ev) {
			Aimeos.focusBefore($(this).closest("tr")).remove();
		});
	},


	configComplete : function() {

		var node = $(".aimeos .item-config");
		$(".config-item .config-key", node).autocomplete({
			source: node.data("keys") || [],
			minLength: 0,
			delay: 0
		});

		$(".aimeos .item").on("click", " .config-key", function(ev) {
			$(this).autocomplete("search", "");
		});
	},


	addConfigMapLine : function() {

		$(".aimeos .item-config").on("click", ".config-map-table .config-map-actions .act-add", function(ev) {

			var node = $(this).closest(".config-map-table");
			var clone = Aimeos.addClone($(".prototype-map", node));

			clone.removeClass("prototype-map");
			$(".act-delete", clone).focus();

			return false;
		});
	},


	deleteConfigMapLine : function() {

		$(".aimeos .item-config").on("click", ".config-map-table .config-map-actions .act-delete", function(ev) {
			Aimeos.focusBefore($(this).closest("tr")).remove();
		});
	},


	hideConfigMap : function() {

		$(".aimeos .item-config").on("click", ".config-map-table .config-map-actions .act-update", function(ev) {

			var obj = {};
			var table = $(this).closest(".config-map-table");
			var lines = $(".config-map-row:not(.prototype-map)", table)

			lines.each(function() {
				obj[ $("input.config-map-key", this).val() ] = $("input.config-map-value", this).val();
			});

			$(".config-value", table.parent()).val(JSON.stringify(obj));

			table.hide();
			lines.remove();

			return false;
		});
	},


	showConfigMap : function() {

		$(".aimeos .item-config").on("focus", ".config-value", function() {

			var table = $(".config-map-table", $(this).parent());

			if(table.is(":visible")) {
				return false;
			}

			try {
				var obj = JSON.parse($(this).val())
			} catch(e) {
				var obj = {};
			}

			for(var key in obj) {
				var clone = Aimeos.addClone($(".prototype-map", table));
				$(".config-map-value", clone).val(obj[key]);
				$(".config-map-key", clone).val(key);
				clone.removeClass("prototype-map");
			}

			table.show();
		});
	}
};



Aimeos.Filter = {

	init : function() {

		this.selectDDInput();
		this.setupFilterOperators();
		this.toggleSearch();
	},


	selectDDInput : function() {

		$(".aimeos .dropdown-menu label").on("click", function(ev) {
			ev.stopPropagation();
			return true;
		});
	},


	selectFilterOperator : function(select, type) {

		var operators = {
			'string': ['=~', '~=', '==', '!='],
			'integer': ['==', '!=', '>', '<', '>=', '<='],
			'datetime': ['>', '<', '>=', '<=', '==', '!='],
			'date': ['>', '<', '>=', '<=', '==', '!='],
			'float': ['>', '<', '>=', '<=', '==', '!='],
			'boolean': ['==', '!='],
		};
		var ops = operators[type];
		var list = [];

		$("option", select).each(function(idx, el) {
			var elem = $(el).removeProp("selected").hide();
			list[elem.val()] = elem;
		});

		if(ops) {
			for(op in ops.reverse()) {
				if(list[ops[op]]) {
					list[ops[op]].remove().show();
					select.prepend(list[ops[op]]);
				}
			};
		}

		$("option", select).first().prop("selected", "selected");
	},


	setupFilterOperators : function() {

		var select = $(".aimeos .main-navbar form .filter-operator");
		var type = $(".aimeos .main-navbar form .filter-key option").first().data("type");

		Aimeos.Filter.selectFilterOperator(select, type);


		$(".aimeos .main-navbar form").on("change", ".filter-key", function(ev) {

			var select = $(".filter-operator", ev.delegateTarget);
			var type = $(":selected", this).data("type");

			Aimeos.Filter.selectFilterOperator(select, type);
		});
	},


	toggleSearch : function() {

		$(".aimeos .main-navbar form").on("click", ".more", function(ev) {
			$(".filter-columns,.filter-key,.filter-operator", ev.delegateTarget).toggle(300, function() {
				$(ev.currentTarget).removeClass("more").addClass("less");
			});
		});

		$(".aimeos .main-navbar form").on("click", ".less", function(ev) {
			$(".filter-columns,.filter-key,.filter-operator", ev.delegateTarget).toggle(300, function() {
				$(ev.currentTarget).removeClass("less").addClass("more");
			});
		});
	}
};



Aimeos.Form = {

	init : function() {

		this.checkFields();
		this.checkSubmit();
		this.createDatePicker();
		this.editFields();
		this.resetSearch();
		this.setupNext();
		this.showErrors();
		this.toggleHelp();
	},


	checkFields : function() {

		$(".aimeos .item-content .readonly").on("change", "input,select", function(ev) {
			$(this).parent().addClass("has-danger");
		});


		$(".aimeos .item-content").on("blur", "input,select", function(ev) {

			if($(this).closest(".readonly").length > 0) {
				return;
			}

			if($(this).is(":invalid") === true) {
				$(this).parent().removeClass("has-success").addClass("has-danger");
			} else {
				$(this).parent().removeClass("has-danger").addClass("has-success");
			}
		});
	},


	checkSubmit : function() {

		$(".aimeos form").each(function() {
			this.noValidate = true;
		});

		$(".aimeos form").on("submit", function(ev) {
			var nodes = [];

			$(".card-header", this).removeClass("has-danger");
			$(".item-navbar .nav-link", this).removeClass("has-danger");

			$(".item-content input,select", this).each(function(idx, element) {
				var elem = $(element);

				if(elem.closest(".prototype").length === 0 && elem.is(":invalid") === true) {
					elem.parent().addClass("has-danger");
					nodes.push(element);
				} else {
					elem.parent().removeClass("has-danger");
				}
			});

			$.each(nodes, function() {
				$(".card-header", $(this).closest(".card")).addClass("has-danger");

				$(this).closest(".tab-pane").each(function() {
					$(".item-navbar .nav-item." + $(this).attr("id") + " .nav-link").addClass("has-danger");
				});
			});

			if( nodes.length > 0 ) {
				$('html, body').animate({
					scrollTop: '0px'
				});

				return false;
			}
		});
	},


	createDatePicker : function() {

		if(typeof Modernizr != 'undefined') {
			if(!Modernizr.inputtypes['datetime-local']) {
				$(".aimeos input[type='datetime-local']").each(function(idx, elem) {
					if($(elem).closest(".prototype").length === 0) {
						$(elem).datepicker({
							dateFormat: 'yy-mm-dd',
							constrainInput: false
						});
					}
				});
			}

			if(Modernizr && !Modernizr.inputtypes['date']) {
				$(".aimeos input[type='date']").each(function(idx, elem) {
					if($(elem).closest(".prototype").length === 0) {
						$(elem).datepicker({
							dateFormat: 'yy-mm-dd',
							constrainInput: false
						});
					}
				});
			}
		}
	},


	editFields : function() {

		$(".aimeos .list-item").on("click", ".act-edit", function(ev) {
			$("[disabled=disabled]", ev.delegateTarget).removeAttr("disabled");
			return false;
		});
	},


	resetSearch : function() {

		$(".aimeos .list-search").on("click", ".act-reset", function(ev) {
			$("select", ev.delegateTarget).val("");
			$("input", ev.delegateTarget).val("");
			return false;
		});
	},


	setupNext : function() {

		$(".aimeos .item").on("click", ".next-action", function(ev) {
			$("#item-next", ev.delegateTarget).val($(this).data('next'));
			$(ev.delegateTarget).submit();
			return false;
		});
	},


	showErrors : function() {

		$(".aimeos .error-list .error-item").each(function() {
			$(".aimeos ." + $(this).data("key") + " .header").addClass("has-danger");
		});
	},


	toggleHelp : function() {

		$(".aimeos").on("click", ".help", function(ev) {
			var list = $(this).closest("table.item-config");

			if( list.length === 0 ) {
				list = $(this).parent();
			}

			$(".help-text", list).slideToggle(300);
		});
	}
};



Aimeos.List = {

	element : null,


	init : function() {

		this.askDelete();
		this.confirmDelete();
	},


	askDelete : function() {
		var self = this;

		$(".aimeos form.list .list-items").on("click", ".act-delete", function(e) {
			$("#confirm-delete").modal("show", $(this));
			self.element = $(this);
			return false;
		});
	},


	confirmDelete : function() {
		var self = this;

		$("#confirm-delete").on("click", ".btn-danger", function(e) {
			if(self.element) {
				window.location = self.element.attr("href");
			}
		});
	}
};



Aimeos.Nav = {

	init : function() {

		this.addShortcuts();
		this.toggleMenu();
	},


	addShortcuts : function() {

		$(document).bind('keydown', function(ev) {
			if(ev.ctrlKey || ev.metaKey) {
				var key = String.fromCharCode(ev.which).toLowerCase();

				if(ev.altKey) {
					if(key.match(/[a-z]/)) {
						ev.preventDefault();
						var link = $(".aimeos .sidebar-menu a[data-ctrlkey=" + key + "]").first();

						if(link.length) {
							window.location = link.attr("href");
						}
						return false;
					}
				}
				switch(key) {
					case 'i':
						ev.preventDefault();
						var node = $(".aimeos :focus").closest(".card,.content-block").find(".act-add:visible").first();
						if(node.length > 0) {
							node.trigger("click");
							return false;
						}

						node = $(".aimeos .act-add:visible").first();
						if(node.attr("href")) {
							window.location = node.attr('href');
						} else {
							node.trigger("click");
						}
						return false;
					case 'd':
						ev.preventDefault();
						var node = $(".aimeos .act-copy:visible").first();
						if(node.attr("href")) {
							window.location = node.attr('href');
						} else {
							node.trigger("click");
						}
						return false;
					case 's':
						ev.preventDefault();
						$(".aimeos form.item").first().submit();
						return false;
				}
			} else if(ev.which === 13) {
				$(".btn:focus").trigger("click");
			}
		});
	},


	toggleMenu : function() {

		$(".aimeos .main-sidebar").on("click", ".separator .more", function(ev) {
			$(".advanced", ev.delegateTarget).slideDown(300, function() {
				$(ev.currentTarget).removeClass("more").addClass("less");
			});
		});

		$(".aimeos .main-sidebar").on("click", ".separator .less", function(ev) {
			$(".advanced", ev.delegateTarget).slideUp(300, function() {
				$(ev.currentTarget).removeClass("less").addClass("more");
			});
		});
	}
};



Aimeos.Tabs = {

	init : function() {

		this.setPanelHeight();
		this.setupTabSwitch();
	},


	setPanelHeight : function() {

		$(".aimeos .tab-pane").on("click", ".filter-columns", function(ev) {
			// CSS class "show" will be added afterwards, thus it's reversed
			var height = ($(this).hasClass("show") ? 0 : $(".dropdown-menu", this).outerHeight());
			$(ev.delegateTarget).css("min-height", $("thead", ev.delegateTarget).outerHeight() + height);
		});
	},


	setupTabSwitch : function() {

		var hash = '';
		var url = document.location.toString();

		if(url.match('#')) {
			hash = url.split('#')[1];
			$('.nav-tabs a[href="#' + hash + '"]').tab('show');

			$("form").each(function() {
				$(this).attr("action", $(this).attr("action").split('#')[0] + '#' + hash);
			});
		}

		$('.nav-tabs a').on('shown.bs.tab', function (e) {
			hash = e.target.hash;

			if(history.pushState) {
				history.pushState(null, null, hash);
			} else {
				window.location.hash = hash;
				window.scrollTo(0, 0);
			}

			$("form").each(function() {
				$(this).attr("action", $(this).attr("action").split('#')[0] + hash);
			});
		})
	}
};




/**
 * Load JSON admin resource definition immediately
 */
Aimeos.options = $.ajax($(".aimeos").data("url"), {
	"method": "OPTIONS",
	"dataType": "json"
});


$(function() {

	Aimeos.init();
	Aimeos.Config.init();
	Aimeos.Filter.init();
	Aimeos.Form.init();
	Aimeos.List.init();
	Aimeos.Nav.init();
	Aimeos.Tabs.init();
});
