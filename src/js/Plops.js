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

	this.toggleCategory = function(type) {
		console.log('toggleCategory '+ type);
		var toggleButton = document.getElementById('toggle' + type);
		var errorBox = document.getElementById('errorBox');
		if (toggleButton.classList.contains('disabled')) {
			// show it
			// also show stack traces  with class of 'type'
			toggleButton.classList.remove('disabled');
			var stackTraces = errorBox.getElementsByClassName(type);			
			for (var i in stackTraces) {
				var stackTrace = stackTraces[i];
				console.log(stackTrace);
				if (stackTrace.classList && stackTrace.classList.contains('exception')) {
					stackTrace.classList.add('visible');
				}
			}
		} else {
			// hide it
			// also hide stack traces with class of 'type'
			toggleButton.classList.add('disabled');
			var stackTraces = errorBox.getElementsByClassName(type);			
			for (var i in stackTraces) {
				var stackTrace = stackTraces[i];
				console.log(stackTrace);
				if (stackTrace.classList && stackTrace.classList.contains('exception')) {
					stackTrace.classList.remove('visible');
				}
			}
		}
	}

	this.construct();
}

var plops = new Plops;