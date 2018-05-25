/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */



Aimeos.Plugin = {

	init : function() {

		Aimeos.Plugin.init();
	}
};



Aimeos.Plugin = {

	init : function() {

		this.setupConfig();
		this.setupDecorator();
		this.setupProvider();
	},


	setupConfig : function() {

		var delegate = $(".aimeos .item-plugin .item-basic");

		if(delegate.length > 0 ) {
			Aimeos.Config.setup('plugin/config', $("input.item-provider", delegate).val(), delegate);
		}

		delegate.on("change input blur", "input.item-provider", function(ev) {
			Aimeos.Config.setup('plugin/config', $(ev.currentTarget).val(), ev.delegateTarget);
		});
	},


	setupDecorator : function() {

		$(".aimeos .item-plugin .item-provider").parent().on("click", ".input-group-addon .decorator-name", function(ev) {

			var name = $(this).data("name");
			var input = $("input.item-provider", ev.delegateTarget);

			if(input.val().indexOf(name) === -1) {
				input.val(input.val() + ',' + name);
				input.trigger("change");
			}
		});
	},


	setupProvider : function() {

		var input = $(".aimeos .item-plugin").on("focus", ".item-provider", function(ev) {

			var type = $(".item-typeid option:selected", ev.delegateTarget).data("code");

			$(this).autocomplete({
				source: $(this).data(type).split(","),
				minLength: 0,
				delay: 0
			});

			$(this).autocomplete("search", "");
		});
	}
};



$(function() {

	Aimeos.Plugin.init();
});
