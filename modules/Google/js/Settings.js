'use strict';

var
	_ = require('underscore'),
	ko = require('knockout'),
	Types = require('%PathToCoreWebclientModule%/js/utils/Types.js')
;

module.exports = {
	ServerModuleName: 'Google',
	HashModuleName: 'google',
	
	Connected: false,
	
	EnableModule: false,
	Id: '',
	Secret: '',
	Key: '',
	Scopes: [],
	
	/**
	 * Initializes settings from AppData object section of this module.
	 * 
	 * @param {Object} oAppDataSection Object contained module settings.
	 */
	init: function (oAppDataSection)
	{
		if (oAppDataSection)
		{
			this.Connected = !!oAppDataSection.Connected;
			
			this.EnableModule = !!oAppDataSection.EnableModule;
			this.Id = Types.pString(oAppDataSection.Id);
			this.Secret = Types.pString(oAppDataSection.Secret);
			this.Key = Types.pString(oAppDataSection.Key);
			this.Scopes = _.isArray(oAppDataSection.Scopes) ? oAppDataSection.Scopes : [];
			
			_.each(this.Scopes, function (oScope){
					oScope.Value = ko.observable(oScope.Value);
				}					
			);
		}
	},
	
	/**
	 * Updates settings that is edited by administrator.
	 * 
	 * @param {boolean} bEnableModule
	 * @param {string} sId
	 * @param {string} sSecret
	 * @param {array} aScopes
	 */
	updateAdmin: function (bEnableModule, sId, sSecret, sKey, aScopes)
	{
		_.each(aScopes, function (oScope){
				oScope.Value = ko.observable(oScope.Value);
			}					
		);
		this.EnableModule = bEnableModule;
		this.Id = sId;
		this.Secret = sSecret;
		this.Key = sKey;
		this.Scopes = aScopes;
	}
};
