(function(window)
{
	var eramba = {
		setPseudoNProgress: function() {
			NProgress.set(0.4);
			setInterval(function(){NProgress.inc();}, 300);
		}
	}

	window.eramba = eramba;
})(window);