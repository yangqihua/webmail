'use strict';

var
	_ = require('underscore'),
	$ = require('jquery'),
	ko = require('knockout'),
	
	TextUtils = require('%PathToCoreWebclientModule%/js/utils/Text.js'),
	
	Browser = require('%PathToCoreWebclientModule%/js/Browser.js'),
	
	Popups = require('%PathToCoreWebclientModule%/js/Popups.js'),
	CAbstractPopup = require('%PathToCoreWebclientModule%/js/popups/CAbstractPopup.js'),
	ConfirmAnotherMessageComposedPopup = require('modules/%ModuleName%/js/popups/ConfirmAnotherMessageComposedPopup.js'),
	
	CComposeView = require('modules/%ModuleName%/js/views/CComposeView.js')
;

/**
 * @constructor
 * @extends CComposePopup
 */
function CComposePopup()
{
	CAbstractPopup.call(this);
	
	CComposeView.call(this);
	
	this.minimized = ko.observable(false);
	
	this.fPreventBackspace = function (ev) {
		var
			bBackspace = ev.which === $.ui.keyCode.BACKSPACE,
			bInput = ev.target.tagName === 'INPUT' || ev.target.tagName === 'TEXTAREA',
			bEditableDiv = ev.target.tagName === 'DIV' && $(ev.target).attr('contenteditable') === 'true'
		;
		
		if (bBackspace && !bInput && !bEditableDiv)
		{
			ev.preventDefault();
			ev.stopPropagation();
		}
	};
	
	this.minimized.subscribe(function () {
		if (this.minimized())
		{
			this.preventBackspaceOff();
		}
		else if (this.shown())
		{
			this.preventBackspaceOn();
		}
	}, this);
	
	this.minimizedTitle = ko.computed(function () {
		return this.subject() || TextUtils.i18n('%MODULENAME%/HEADING_MINIMIZED_NEW_MESSAGE');
	}, this);
}

_.extendOwn(CComposePopup.prototype, CAbstractPopup.prototype);

_.extendOwn(CComposePopup.prototype, CComposeView.prototype);

CComposePopup.prototype.PopupTemplate = '%ModuleName%_ComposePopup';

CComposePopup.prototype.preventBackspaceOn = function ()
{
	$(document).on('keydown', this.fPreventBackspace);
};

CComposePopup.prototype.preventBackspaceOff = function ()
{
	$(document).off('keydown', this.fPreventBackspace);
};

CComposePopup.prototype.onHide = function ()
{
	this.preventBackspaceOff();
};

/**
 * @param {Array} aParams
 */
CComposePopup.prototype.onShow = function (aParams)
{
	aParams = aParams || [];
	
	if (aParams.length === 1 && aParams[0] === 'close')
	{
		this.closePopup();
	}
	else
	{
		var
			bOpeningSameDraft = aParams.length === 3 && aParams[0] === 'drafts' && aParams[2] === this.draftUid(),
			bWasMinimized = this.minimized()
		;
		
		this.maximize();
		if (this.shown())
		{
			if (aParams.length > 0 && !bOpeningSameDraft)
			{
				if (this.hasSomethingToSave())
				{
					this.stopAutosaveTimer();
					Popups.showPopup(ConfirmAnotherMessageComposedPopup, [_.bind(function (sAnswer) {
						switch (sAnswer)
						{
							case Enums.AnotherMessageComposedAnswer.Discard:
								this.onRoute(aParams);
								break;
							case Enums.AnotherMessageComposedAnswer.SaveAsDraft:
								if (this.hasSomethingToSave())
								{
									this.executeSave(true, false);
								}
								this.onRoute(aParams);
								break;
							case Enums.AnotherMessageComposedAnswer.Cancel:
								break;
						}
						this.startAutosaveTimer();
					}, this)]);
				}
				else
				{
					this.onRoute(aParams);
				}
			}
			else if (!bWasMinimized)
			{
				this.onRoute(aParams);
			}

			this.oHtmlEditor.oCrea.clearUndoRedo();
		}
		else
		{
			CComposeView.prototype.onShow.call(this);
			this.onRoute(aParams);
		}
		this.preventBackspaceOn();
	}
};

CComposePopup.prototype.minimize = function ()
{
	this.minimized(true);
	this.$popupDom.addClass('minimized');
};

CComposePopup.prototype.maximize = function ()
{
	this.minimized(false);
	this.$popupDom.removeClass('minimized');
};

CComposePopup.prototype.saveAndClose = function ()
{
	if (this.hasSomethingToSave())
	{
		this.saveCommand();
	}

	this.closePopup();
};

CComposePopup.prototype.cancelPopup = function ()
{
	if (this.hasSomethingToSave())
	{
		this.minimize();
	}
	else
	{
		this.closePopup();
	}
};

/**
 * @param {Object} oEvent
 */
CComposePopup.prototype.onEscHandler = function (oEvent)
{
	var
		bHtmlEditorHasOpenedPopup = this.oHtmlEditor.hasOpenedPopup(),
		bOnFileInput = !Browser.ie && oEvent.target && (oEvent.target.tagName.toLowerCase() === 'input') && (oEvent.target.type.toLowerCase() === 'file')
	;
	
	if (bOnFileInput)
	{
		oEvent.target.blur();
	}
	
	if (Popups.hasOnlyOneOpenedPopup() && !bHtmlEditorHasOpenedPopup && !bOnFileInput)
	{
		this.minimize();
	}
	
	if (bHtmlEditorHasOpenedPopup)
	{
		this.oHtmlEditor.closeAllPopups();
	}
};

module.exports = new CComposePopup();
