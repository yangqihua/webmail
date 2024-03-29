'use strict';

var
	_ = require('underscore'),
	$ = require('jquery'),
	
	TextUtils = require('%PathToCoreWebclientModule%/js/utils/Text.js'),
	
	Settings = require('%PathToCoreWebclientModule%/js/Settings.js'),
	
	AppData = window.auroraAppData,
	
	oModules = {}
;

module.exports = {
	init: function (oAvaliableModules) {
		_.each(oAvaliableModules, function (fModuleConstructor, sModuleName) {
			if (_.isFunction(fModuleConstructor))
			{
				var oModule = fModuleConstructor(AppData);
				if (oModule)
				{
					oModules[sModuleName] = oModule;
				}
			}
		});
		
		if (Settings.AllowChangeSettings)
		{
			this.run('SettingsWebclient', 'registerSettingsTab', [function () { return require('%PathToCoreWebclientModule%/js/views/CommonSettingsPaneView.js'); }, 'common', TextUtils.i18n('%MODULENAME%/LABEL_COMMON_SETTINGS_TABNAME')]);
		}
		
		_.each(oModules, _.bind(function (oModule) {
			if ($.isFunction(oModule.start))
			{
				oModule.start(this);
			}
		}, this));
	},
	
	getModulesScreens: function () {
		var oModulesScreens = {};
		
		_.each(oModules, function (oModule, sModuleName) {
			if (_.isFunction(oModule.getScreens))
			{
				oModulesScreens[sModuleName] = oModule.getScreens();
			}
		});
		
		return oModulesScreens;
	},
	
	getModulesTabs: function (bOnlyStandard) {
		if (!_.isArray(this.aTabs))
		{
			this.aTabs = [];
			this.aStandardTabs = [];
			_.each(oModules, _.bind(function (oModule, sModuleName) {
				if ($.isFunction(oModule.getHeaderItem))
				{
					var oHeaderItem = oModule.getHeaderItem();
					if (oHeaderItem && oHeaderItem.item)
					{
						if ($.isFunction(oHeaderItem.item.setName))
						{
							oHeaderItem.item.setName(oHeaderItem.name || sModuleName);
							this.aStandardTabs.push(oHeaderItem.item);
						}
						this.aTabs.push(oHeaderItem.item);

						if (oModules[sModuleName] && oModules[sModuleName].enableModule)
						{
							oHeaderItem.item.visible(oModules[sModuleName].enableModule());
							oModules[sModuleName].enableModule.subscribe(function (bEnableModule) {
								oHeaderItem.item.visible(bEnableModule);
							});
						}
					}
				}
			}, this));
			
			this.aStandardTabs = _.sortBy(this.aStandardTabs, function (oTab) {
				var iIndex = _.indexOf(Settings.HeaderModulesOrder, oTab.sName);
				return iIndex !== -1 ? iIndex : Settings.HeaderModulesOrder.length;
			});
			
			this.aTabs = _.sortBy(this.aTabs, function (oTab) {
				var iIndex = _.indexOf(Settings.HeaderModulesOrder, oTab.sName);
				return iIndex !== -1 ? iIndex : Settings.HeaderModulesOrder.length;
			});
		}
		
		//TODO actualy, the item's order of aTabs affects on order of tabs in html
		return bOnlyStandard ? this.aStandardTabs : this.aTabs;
	},
	
	getModulesPrefetchers: function ()
	{
		var aPrefetchers = [];

		_.each(oModules, function (oModule, sModuleName) {
			if ($.isFunction(oModule.getPrefetcher))
			{
				aPrefetchers.push(oModule.getPrefetcher());
			}
		});

		return aPrefetchers;
	},
	
	isModuleIncluded: function (sModuleName)
	{
		return oModules[sModuleName] !== undefined;
	},
	
	isModuleEnabled: function (sModuleName)
	{
		return oModules[sModuleName] && (!oModules[sModuleName].enableModule || oModules[sModuleName].enableModule());
	},
	
	/**
	 * Calls a specified function of a specified module if the module and the function exist and are available.
	 * @param {string} sModuleName Name of the module.
	 * @param {string} sFunctionName Name of the function.
	 * @param {Array} aParams Array of parameters that will be passed to the function.
	 * @returns {Boolean}
	 */
	run: function (sModuleName, sFunctionName, aParams)
	{
		if (this.isModuleEnabled(sModuleName))
		{
			var oModule = oModules[sModuleName];

			if (oModule && $.isFunction(oModule[sFunctionName]))
			{
				if (!_.isArray(aParams))
				{
					aParams = [];
				}

				return oModule[sFunctionName].apply(oModule, aParams);
			}
		}
		
		return false;
	}
};
