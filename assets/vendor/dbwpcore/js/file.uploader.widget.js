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
			buttonUploadSelector: '.js-upload-button',
			hiddenClass: 'd-none'
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
			this.disableUploadButton();
			this.addEvents();
		},

		addEvents: function()
		{
			var events = {};

			events['click ' + this.options.buttonUploadSelector] = function(ev) {
				ev.preventDefault();
				this.upload();
			};

			events['change ' + this.options.fileUploadSelector] = function(ev) {
				ev.preventDefault();
				this.checkEnable();
			};

			this._on(events);
		},

		disableUploadButton: function()
		{
			this.getButtonUpload().attr('disabled', 'disabled');
			return this;
		},

		enableUploadButton: function()
		{
			this.getButtonUpload().removeAttr('disabled');
			return this;
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
			this.getFileUpload().addClass(this.options.hiddenClass);
			return this;
		},

		showFileUpload: function()
		{
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

		getButtonUpload: function()
		{
			return $(this.options.buttonUploadSelector, this.element);
		},

		getInnerProgressBar: function()
		{
			return $(this.options.progressInnerSelector, this.getProgressBar());
		},

		checkEnable()
		{
			if(this.getFileUpload().val()){
				this.enableUploadButton()
			}else{
				this.disableUploadButton();
			}

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
				throw 'File not selected';
			}

			var formData = new FormData();

			for(var i = 0; i < uploader.files.length; i++){
				formData.append('file_' + i, uploader.files[i]);
			}

			this.request = new XMLHttpRequest();

			if(this.request.upload) {
				this.request.upload.onprogress = function (data) {
					self.updatePercentage(self.getPercentage(data.loaded, data.total));
				};

				this.request.addEventListener('load', function(){
					self.getFileUpload().val('');
					self.showFileUpload().hideProgressBar();
				});
			}

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