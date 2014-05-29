/**
 * @name        Visible Module
 * @description	Ce module permet de retourner si un élément est visible ou non.
 * @author		Tobias Reich _ Chapon Theo
 * @copyright	2014 by Tobias Reich
 */

visible = {

	albums: function() {
		if ($("#tools_albums").css("display")==="block") return true;
		else return false;
	},

	album: function() {
		if ($("#tools_album").css("display")==="block") return true;
		else return false;
	},

	photo: function() {
		if ($("#imageview.fadeIn").length>0) return true;
		else return false;
	},

	infobox: function() {
		if ($("#infobox.active").length>0) return true;
		else return false;
	},

	controls: function() {
		if (lychee.loadingBar.css("opacity")<1) return false;
		else return true;
	},

	message: function() {
		if ($(".message").length>0) return true;
		else return false;
	},
	
	welcome: function() {
		if ($(".welcome").length>0) return true;
		else return false;
	},

	signin: function() {
		if ($(".message .sign_in").length>0) return true;
		else return false;
	},

	contextMenu: function() {
		if ($(".contextmenu").length>0) return true;
		else return false;
	}

}