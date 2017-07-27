/**
 * progressButton.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */
 
(function( window ) {
	'use strict';
	// class helper functions from bonzo https://github.com/ded/bonzo
	function classReg(className) {
		return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
	}
	// classList support for class management
	// altho to be fair, the api sucks because it won't accept multiple classes at once
	var hasClass, addClass, removeClass;
	if ('classList' in document.documentElement) {
		hasClass = function( elem, c ) {
			return elem.classList.contains( c );
		};
		addClass = function(elem, c) {
			elem.classList.add(c);
		};
		removeClass = function(elem, c) {
			elem.classList.remove(c);
		};
	} else {
		hasClass = function(elem, c) {
			return classReg(c).test( elem.className );
		};
		addClass = function(elem, c) {
			if (!hasClass(elem, c) ) {
				elem.className = elem.className + ' ' + c;
			}
		};
		removeClass = function( elem, c ) {
			elem.className = elem.className.replace( classReg( c ), ' ' );
		};
	}
	function toggleClass( elem, c ) {
		var fn = hasClass( elem, c ) ? removeClass : addClass;
		fn( elem, c );
	}
	var classie = {
		// full names
		hasClass: hasClass,
		addClass: addClass,
		removeClass: removeClass,
		toggleClass: toggleClass,
		// short names
		has: hasClass,
		add: addClass,
		remove: removeClass,
		toggle: toggleClass
	};
	// transport
	if (typeof define === 'function' && define.amd) {
		// AMD
		define(classie);
	} else {
		// browser global
		window.classie = classie;
	}
})(window);
;(function(window) {
	'use strict';
	// https://gist.github.com/edankwan/4389601
	function extend(a, b) {
		for(var key in b) { 
			if( b.hasOwnProperty(key) ) {
				a[key] = b[key];
			}
		}
		return a;
	}
	function ProgressButton(el, options) {
		this.button = el;
		this.options = extend( {}, this.options );
  		extend( this.options, options );
  		this._init();
	}
	ProgressButton.prototype.options = {
		// time in ms that the status (success or error will be displayed)
		// during this time the button will be disabled
		statusTime : 4000
	};
	ProgressButton.prototype._init = function() {
		this._validate();
		// create structure
		this._create();
		// init events
		this._initEvents();
	};
	ProgressButton.prototype._validate = function() {
		// we will consider the fill/horizontal as default
		if(this.button.getAttribute('data-style') === null) {
			this.button.setAttribute('data-style', 'fill');
		}
		if(this.button.getAttribute('data-vertical') === null && this.button.getAttribute('data-horizontal') === null) {
			this.button.setAttribute('data-vertical', '');
		}
	};
	ProgressButton.prototype._create = function() {
		var textEl = document.createElement('span');
		textEl.className = 'content';
		textEl.innerHTML = this.button.innerHTML;
		var progressEl = document.createElement('span');
		progressEl.className = 'progress';
		var progressInnerEl = document.createElement('span');
		progressInnerEl.className = 'progress-inner';
		progressEl.appendChild(progressInnerEl);
		// clear content
		this.button.innerHTML = '';
		if(this.button.getAttribute('data-perspective') !== null ) {
			var progressWrapEl = document.createElement('span');
			progressWrapEl.className = 'progress-wrap';
			progressWrapEl.appendChild(textEl);
			progressWrapEl.appendChild(progressEl);
			this.button.appendChild(progressWrapEl);
		} else {
			this.button.appendChild(textEl);
			this.button.appendChild(progressEl);
		}
		// the element that serves as the progress bar
		this.progress = progressInnerEl;
		// property to change on the progress element
		if(this.button.getAttribute('data-horizontal') !== null) {
			this.progressProp = 'width';
		}
		else if(this.button.getAttribute('data-vertical') !== null) {
			this.progressProp = 'height';
		}
		this._enable();
	};
	ProgressButton.prototype._setProgress = function(val) {
		this.progress.style[this.progressProp] = 100 * val + '%';
	};
	ProgressButton.prototype._initEvents = function() {
		var self = this;
		this.button.addEventListener('click', function() {
			// disable the button
			//self.button.setAttribute('disabled', '');
			// add class state-loading to the button (applies a specific transform to the button depending which data-style is defined - defined in the stylesheets)
			classie.remove(self.progress, 'notransition');
			classie.add(this, 'state-loading');
			setTimeout(function() {
				if(typeof self.options.callback === 'function') {
					self.options.callback( self );
				}
				else {
					self._setProgress( 1 );
					var onEndTransFn = function( ev ) {
						//this.removeEventListener( transEndEventName, onEndTransFn );
						self._stop();
					};
					onEndTransFn.call();
				}
			}, 
			self.button.getAttribute( 'data-style' ) === 'fill' ? 0 : 200 ); // TODO: change timeout to transitionend event callback
		});
	};
	ProgressButton.prototype._stop = function( status ) {
		var self = this;
		setTimeout( function() {
			// fade out progress bar
			self.progress.style.opacity = 0;
			var onEndTransFn = function( ev ) {
				//this.removeEventListener( transEndEventName, onEndTransFn );
				classie.add( self.progress, 'notransition' );
				self.progress.style[ self.progressProp ] = '0%';
				self.progress.style.opacity = 1;
			};
			onEndTransFn.call();
			// add class state-success to the button
			if( typeof status === 'number' ) {
				var statusClass = status >= 0 ? 'state-success' : 'state-error';
				classie.add( self.button, statusClass );
				// after options.statusTime remove status
				setTimeout( function() {
					classie.remove( self.button, statusClass );
					self._enable();
				}, self.options.statusTime );
			} else {
				self._enable();
			}
			// remove class state-loading from the button
			classie.remove( self.button, 'state-loading' );
		}, 100 );
	};
	// enable button
	ProgressButton.prototype._enable = function() {
		this.button.removeAttribute( 'disabled' );
	}
	// add to global namespace
	window.ProgressButton = ProgressButton;
})( window );