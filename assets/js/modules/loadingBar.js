/**
 * @name		LoadingBar Module
 * @description	Ce module permet d'afficher la barre de chargement lors de requêtes envoyées au serveur comme 
 * l'upload ou l'affichage des albums.
 * @author		Tobias Reich _ Theo Chapon
 * @copyright	2014 by Tobias Reich
 */

loadingBar = {

	status: null,
	
	// permet d'afficher la bar de chargement ou la barre d'erreur si besoin (statut)
	show: function(status, errorText) {

		if (status==="error") {

			loadingBar.status = "error";

			if (!errorText) errorText = "Whoops, it looks like something went wrong. Please reload the site and try again!"

			lychee.loadingBar
				.removeClass("loading uploading error")
				.addClass(status)
				.html("<h1>Error: <span>" + errorText + "</span></h1>")
				.show()
				.css("height", "40px");
			if (visible.controls()) lychee.header.addClass("error");

			clearTimeout(lychee.loadingBar.data("timeout"));
			lychee.loadingBar.data("timeout", setTimeout(function() { loadingBar.hide(true) }, 3000));

		} else if (loadingBar.status==null) {

			loadingBar.status = "loading";

			clearTimeout(lychee.loadingBar.data("timeout"));
			lychee.loadingBar.data("timeout", setTimeout(function() {
				lychee.loadingBar
					.show()
					.removeClass("loading uploading error")
					.addClass("loading");
				if (visible.controls()) lychee.header.addClass("loading");
			}, 1000));

		}

	},

	//efface la barre de chargement
	hide: function(force_hide) {

		if ((loadingBar.status!=="error"&&loadingBar.status!=null)||force_hide) {

			loadingBar.status = null;
			clearTimeout(lychee.loadingBar.data("timeout"));
			lychee.loadingBar.html("").css("height", "3px");
			if (visible.controls()) lychee.header.removeClass("error loading");
			setTimeout(function() { lychee.loadingBar.hide() }, 300);

		}

	}

}