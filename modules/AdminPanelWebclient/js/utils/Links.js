'use strict';

var
	_ = require('underscore'),
	
	Types = require('%PathToCoreWebclientModule%/js/utils/Types.js'),
	
	Settings = require('modules/%ModuleName%/js/Settings.js'),
	
	Links = {}
;

/**
 * @param {Array=} aEntities
 * @param {string=} sCurrEntityType = ''
 * @param {string=} sLast = ''
 * @return {Array}
 */
Links.get = function (sCurrEntityType, aEntities, sLast)
{
	var aResult = [Settings.HashModuleName];
	
	aEntities = aEntities || [];
	
	_.each(Settings.EntitiesData, function (oEntityData) {
		if (Types.isPositiveNumber(aEntities[oEntityData.Type]))
		{
			aResult.push(oEntityData.ScreenHash.substr(0,1) + aEntities[oEntityData.Type]);
		}
		else if (sCurrEntityType === oEntityData.Type)
		{
			aResult.push(oEntityData.ScreenHash);
		}
	});
	
	if (Types.isNonEmptyString(sLast))
	{
		aResult.push(sLast);
	}
	
	return aResult;
};

/**
 * @param {Array} aParams
 * 
 * @return {Object}
 */
Links.parse = function (aParams)
{
	var
		iIndex = 0,
		oEntities = {},
		sCurrEntityType = ''
	;
	
	_.each(Settings.EntitiesData, function (oEntityData) {
		if (aParams[iIndex] && oEntityData.ScreenHash === aParams[iIndex])
		{
			sCurrEntityType = oEntityData.Type;
			iIndex++;
		}
		if (aParams[iIndex] && oEntityData.ScreenHash.substr(0, 1) === aParams[iIndex].substr(0, 1) && Types.pInt(aParams[iIndex].substr(1)) > 0)
		{
			oEntities[oEntityData.Type] = Types.pInt(aParams[iIndex].substr(1));
			sCurrEntityType = oEntityData.Type;
			iIndex++;
		}
	});
	
	return {
		Entities: oEntities,
		CurrentType: sCurrEntityType,
		Last: Types.isNonEmptyString(aParams[iIndex]) ? aParams[iIndex] : ''
	};
};

module.exports = Links;
