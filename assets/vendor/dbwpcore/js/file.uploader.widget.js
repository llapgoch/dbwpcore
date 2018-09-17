;(function($){
	var DEFAULT_INIT_SELECTOR = '.js-file-uploader-component';

	$.widget('dbwpcore.fileUploader', {
		options: {
			jsDataKey: 'jsData',
			endpointDataKey: 'endpoint',
			uploaderComponentsSelector: '.js-upload-components',
			progressBarSelector: '.js-progress-bar',
			progressInnerSelector: '.progress-bar',
			fileUploadSelector: '.js-file-input',
			labelSelector: '.js-file-label',
			hiddenClass: 'd-none',
			uploadErrorMessage: 'An error occurred during the upload'
		},

		jsData: null,
		request: null,
		endpoint: '',

		_create: function(){
			this._super();
			this.jsData = this.element.data(this.options.jsDataKey);

			if(!this.jsData || !this.jsData[this.options.endpointDataKey]){
				throw 'Uploader must have endpoint in its data array'
			}

			this.endpoint = this.jsData[this.options.endpointDataKey];

			this.hideProgressBar();
			this.showFileUpload();
			this.addEvents();
		},

		addEvents: function()
		{
			var events = {};

			events['change ' + this.options.fileUploadSelector] = function(ev) {
				ev.preventDefault();
				this.upload();
			};

			this._on(events);
		},

		hideProgressBar: function()
		{
			this.getProgressBar().addClass(this.options.hiddenClass);
			return this;
		},

		showProgressBar: function()
		{
			this.getProgressBar().removeClass(this.options.hiddenClass);
			return this;
		},

		hideFileUpload: function()
		{
			this.getLabel().addClass(this.options.hiddenClass);
			this.getFileUpload().addClass(this.options.hiddenClass);
			return this;
		},

		showFileUpload: function()
		{
			this.getLabel().removeClass(this.options.hiddenClass);
			this.getFileUpload().removeClass(this.options.hiddenClass);
			return this;
		},

		getProgressBar: function()
		{
			return $(this.options.progressBarSelector, this.element);
		},

		getFileUpload: function()
		{
			return $(this.options.fileUploadSelector, this.element);
		},

		getLabel: function()
		{
			return $(this.options.labelSelector, this.element);
		},

		getInnerProgressBar: function()
		{
			return $(this.options.progressInnerSelector, this.getProgressBar());
		},

		checkEnable()
		{
			return this;
		},

		getPercentage: function(amount, total)
		{
			return Math.round((amount / total) * 100);
		},

		updatePercentage(percentage)
		{
			var $innerBar = this.getInnerProgressBar();
			percentage = Math.max(0, Math.min(100, percentage)) + '%';

			$innerBar.css('width', percentage).html(percentage);
			return this;
		},

		upload: function()
		{
			var self = this;

			if(this.request){
				try{
					this.request.abort();
				} catch (e) {
					// IE
				}
			}

			var uploader = this.getFileUpload().get(0);
			if(!uploader || !uploader.files || !uploader.files[0]){
				throw 'No file selected';
			}

			self.updatePercentage(0);

			var formData = new FormData();
			for(var i = 0; i < uploader.files.length; i++){
				formData.append('file_' + i, uploader.files[i]);
			}

			this.request = new XMLHttpRequest();

			if(this.request.upload) {
				this.request.upload.addEventListener('progress', function (data) {
					self.updatePercentage(self.getPercentage(data.loaded, data.total));
				});
			}

			this.request.addEventListener('loadend', function(ev){
				var xhr = ev.currentTarget;

				self.updatePercentage(100);
				self.getFileUpload().val('');
				self.showFileUpload().hideProgressBar();

				window.setTimeout(function(){
					try {
						if (xhr.response) {
							xhr.responseJSON = JSON.parse(xhr.response);
						}
					} catch (e){
						return alert(self.options.uploadErrorMessage);
					}

					if(xhr.status !== 200){
						if(xhr.responseJSON){
							alert(xhr.responseJSON.message);
						}else{
							alert(self.options.uploadErrorMessage);
						}
					}else{
						$(document).trigger('ajaxSuccess', xhr);
					}
				}, 10);
			});

			this.request.open('POST', this.endpoint, true);
			this.request.send(formData);

			this.hideFileUpload();
			this.showProgressBar();
		}
	});

	function initialise(){
		$(DEFAULT_INIT_SELECTOR).fileUploader();
	}

	$(document).on('ready.dbwpcorefileuploader', function(){
		initialise();
	});
}(jQuery));