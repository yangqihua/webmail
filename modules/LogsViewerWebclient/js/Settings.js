'use strict';

var Types = require('%PathToCoreWebclientModule%/js/utils/Types.js');

module.exports = {
	ServerModuleName: 'LogsViewerWebclient',
	HashModuleName: 'logs-viewer',
	
	EnableLogging: false,
	EnableEventLogging: false,
	LoggingLevel: 0,
	LogSizeBytes: 0,
	EventLogSizeBytes: 0,
	LogFileName: '',
	EventLogFileName: '',
	
	/**
	 * Initializes settings of the module.
	 * 
	 * @param {Object} oAppDataSection module section in AppData.
	 */
	init: function (oAppDataSection)
	{
		if (oAppDataSection)
		{
			this.EnableLogging = !!oAppDataSection.EnableLogging;
			this.EnableEventLogging = !!oAppDataSection.EnableEventLogging;
			this.LoggingLevel = Types.pInt(oAppDataSection.LoggingLevel);
			this.updateLogsData(oAppDataSection.LogFilesData);
			this.ELogLevel = oAppDataSection.ELogLevel;
		}
	},
	
	updateLogging: function (bEnableLogging, bEnableEventLogging, iLoggingLevel)
	{
		this.EnableLogging = !!bEnableLogging;
		this.EnableEventLogging = !!bEnableEventLogging;
		this.LoggingLevel = Types.pInt(iLoggingLevel);
	},
	
	updateLogsData: function (oLogFilesData)
	{
		this.LogSizeBytes = Types.pInt(oLogFilesData.LogSizeBytes);
		this.EventLogSizeBytes = Types.pInt(oLogFilesData.EventLogSizeBytes);
		this.LogFileName = Types.pString(oLogFilesData.LogFileName);
		this.EventLogFileName = Types.pString(oLogFilesData.EventLogFileName);
	}
};
