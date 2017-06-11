function Plops() {
	
	var self = this;

	this.construct = function() {
		document.addEventListener("keypress", function(event) {
		    var isShift = event.shiftKey
		    if (event.keyCode == 13 && isShift) {
		        self.toggleHelpBox();
		    }
		});

	}

	this.toggleHelpBox = function() {
		var errorBox = document.getElementById('errorBox');
		if (errorBox.classList.contains('visible')) {
			// hide it
			errorBox.classList.remove('visible');
		} else {
			// show it
			errorBox.classList.add('visible');
		}
	}

	this.showTrace = function(id) {
		var stackTrace = document.getElementById('stackTrace' + id);
		if (stackTrace.classList.contains('visible')) {
			// hide it
			stackTrace.classList.remove('visible');
		} else {
			// show it
			stackTrace.classList.add('visible');
		}
	}

	this.construct();
}

var plops = new Plops;