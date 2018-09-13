;(function($){
	var DEFAULT_INIT_SELECTOR = '.js-file-uploader-component';

	$.widget('dbwpcore.fileUploader', {
		options: {
			uploaderComponentsSelector: '.js-upload-components',
			progressBarSelector: '.js-progress-bar',
			fileUploadSelector: '.js-file-upload',
			hiddenClass: 'd-none'
		},

		_create: function(){
			this.hideProgressBar();
			this.showFileUpload();
		},

		hideProgressBar: function(){
			this.getProgressBar().addClass(this.options.hiddenClass);
		},

		showProgressBar: function(){
			this.getProgressBar().removeClass(this.options.hiddenClass);
		},

		hideFileUpload: function(){
			this.getFileUpload().addClass(this.options.hiddenClass);
		},

		showFileUpload: function(){
			this.getFileUpload().removeClass(this.options.hiddenClass);
		},

		getProgressBar: function(){
			return $(this.options.progressBarSelector, this.element);
		},

		getFileUpload: function(){
			return $(this.options.fileUploadSelector, this.element);
		}
	});

	function initialise(){
		$(DEFAULT_INIT_SELECTOR).fileUploader();
	}

	$(document).on('ready.dbwpcorefileuploader', function(){
		initialise();
	});
}(jQuery));