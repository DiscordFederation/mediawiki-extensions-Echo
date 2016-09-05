<?php
/**
 * MediaWiki Extension: Echo
 * http://www.mediawiki.org/wiki/Extension:Echo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * This program is distributed WITHOUT ANY WARRANTY.
 */

/**
 *
 * @file
 * @ingroup Extensions
 * @author Andrew Garrett, Benny Situ, Ryan Kaldari, Erik Bernhardson
 * @licence MIT License
 */

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install this extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/Echo/Echo.php" );
EOT;
	exit( 1 );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'Echo',
	'url' => 'https://www.mediawiki.org/wiki/Extension:Echo',
	'author' => array( 'Andrew Garrett', 'Ryan Kaldari', 'Benny Situ', 'Luke Welling', 'Kunal Mehta', 'Moriel Schottlender', 'Jon Robson' ),
	'descriptionmsg' => 'echo-desc',
	'license-name' => 'MIT',
);

$wgMessagesDirs['Echo'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['EchoAliases'] = __DIR__ . '/Echo.alias.php';

// This file is autogenerated by scripts/gen-autoload.php
require __DIR__ . "/autoload.php";

// Queable jobs
$wgJobClasses['EchoNotificationJob'] = 'EchoNotificationJob';
// Job to delete older notifications
$wgJobClasses['EchoNotificationDeleteJob'] = 'EchoNotificationDeleteJob';

// API
$wgAPIMetaModules['notifications'] = 'ApiEchoNotifications';
$wgAPIMetaModules['unreadnotificationpages'] = 'ApiEchoUnreadNotificationPages';
$wgAPIModules['echomarkread'] = 'ApiEchoMarkRead';
$wgAPIModules['echomarkseen'] = 'ApiEchoMarkSeen';

// Special page
$wgSpecialPages['Notifications'] = 'SpecialNotifications';
$wgSpecialPages['DisplayNotificationsConfiguration'] = 'SpecialDisplayNotificationsConfiguration';
$wgSpecialPages['NotificationsMarkRead'] = 'SpecialNotificationsMarkRead';

// Housekeeping hooks
$wgHooks['LoadExtensionSchemaUpdates'][] = 'EchoHooks::onLoadExtensionSchemaUpdates';
$wgHooks['GetPreferences'][] = 'EchoHooks::getPreferences';
$wgHooks['PersonalUrls'][] = 'EchoHooks::onPersonalUrls';
$wgHooks['BeforePageDisplay'][] = 'EchoHooks::beforePageDisplay';
$wgHooks['MakeGlobalVariablesScript'][] = 'EchoHooks::makeGlobalVariablesScript';
$wgHooks['UnitTestsList'][] = 'EchoHooks::getUnitTests';
$wgHooks['ResourceLoaderRegisterModules'][] = 'EchoHooks::onResourceLoaderRegisterModules';
$wgHooks['EventLoggingRegisterSchemas'][] = 'EchoHooks::onEventLoggingRegisterSchemas';
$wgHooks['ResourceLoaderTestModules'][] = 'EchoHooks::onResourceLoaderTestModules';
$wgHooks['UserGroupsChanged'][] = 'EchoHooks::onUserGroupsChanged';
$wgHooks['UserLoadOptions'][] = 'EchoHooks::onUserLoadOptions';
$wgHooks['UserSaveOptions'][] = 'EchoHooks::onUserSaveOptions';
$wgHooks['UserClearNewTalkNotification'][] = 'EchoHooks::onUserClearNewTalkNotification';
$wgHooks['ParserTestTables'][] = 'EchoHooks::onParserTestTables';
$wgHooks['EmailUserComplete'][] = 'EchoHooks::onEmailUserComplete';
$wgHooks['LoginFormValidErrorMessages'][] = 'EchoHooks::onLoginFormValidErrorMessages';
$wgHooks['OutputPageCheckLastModified'][] = 'EchoHooks::onOutputPageCheckLastModified';
$wgHooks['ArticleDeleteComplete'][] = 'EchoHooks::onArticleDeleteComplete';
$wgHooks['ArticleUndelete'][] = 'EchoHooks::onArticleUndelete';


// Extension:UserMerge support
$wgHooks['UserMergeAccountFields'][] = 'EchoHooks::onUserMergeAccountFields';
$wgHooks['MergeAccountFromTo'][] = 'EchoHooks::onMergeAccountFromTo';
$wgHooks['UserMergeAccountDeleteTables'][] = 'EchoHooks::onUserMergeAccountDeleteTables';

// Extension initialization
$wgExtensionFunctions[] = 'EchoHooks::initEchoExtension';

require __DIR__ . '/Resources.php';

$wgHooks['EchoGetBundleRules'][] = 'EchoHooks::onEchoGetBundleRules';
$wgHooks['EchoAbortEmailNotification'][] = 'EchoHooks::onEchoAbortEmailNotification';

// Hook appropriate events
$wgHooks['ArticleSaveComplete'][] = 'EchoHooks::onArticleSaved';
$wgHooks['LocalUserCreated'][] = 'EchoHooks::onLocalUserCreated';
$wgHooks['ArticleRollbackComplete'][] = 'EchoHooks::onRollbackComplete';
$wgHooks['UserSaveSettings'][] = 'EchoHooks::onUserSaveSettings';

// Disable ordinary user talk page email notifications
$wgHooks['AbortTalkPageEmailNotification'][] = 'EchoHooks::onAbortTalkPageEmailNotification';
$wgHooks['SendWatchlistEmailNotification'][] = 'EchoHooks::onSendWatchlistEmailNotification';
// Disable the orange bar of death
$wgHooks['GetNewMessagesAlert'][] = 'EchoHooks::abortNewMessagesAlert';
$wgHooks['LinksUpdateAfterInsert'][] = 'EchoHooks::onLinksUpdateAfterInsert';

// Beta features
$wgHooks['GetBetaFeaturePreferences'][] = 'EchoHooks::getBetaFeaturePreferences';

// Global config vars
$wgHooks['ResourceLoaderGetConfigVars'][] = 'EchoHooks::onResourceLoaderGetConfigVars';

// Configuration

// Whether to turn on email batch function
$wgEchoEnableEmailBatch = true;

// Whether to use job queue to process web and email notifications, bypass the queue for now
// since it's taking more than an hour to run in mediawiki.org, this is not acceptable for the
// purpose of testing notification.
$wgEchoUseJobQueue = false;

// The organization address, the value should be defined in LocalSettings.php
$wgEchoEmailFooterAddress = '';

// The email address for both "from" and "reply to" on email notifications.
// Should be defined in LocalSettings.php
$wgNotificationSender = $wgPasswordSender;
// Name for "from" on email notifications. Should be defined in LocalSettings.php
// if null, uses 'emailsender' message
$wgNotificationSenderName = null;
// Name for "reply to" on email notifications. Should be defined in LocalSettings.php
$wgNotificationReplyName = 'No Reply';

// Use the main db if this is set to false, to use a specific external db, just
// use any key defined in $wgExternalServers
$wgEchoCluster = false;

// Shared database to use for keeping track of cross-wiki unread notifications
// false to not keep track of it at all
$wgEchoSharedTrackingDB = false;

// Cluster the shared tracking database is located on, false if it is on the
// main one. Must be a key defined in $wgExternalServers
$wgEchoSharedTrackingCluster = false;

// Enable this when you've changed the section (alert vs message) of a notification
// type, but haven't yet finished running backfillUnreadWikis.php. This setting
// reduces performance but prevents glitchy and inaccurate information from being
// show to users while the unread_wikis table is being rebuilt.
$wgEchoSectionTransition = false;

// Enable this when you've changed the way bundled notifications are counted,
// but haven't yet finished running backfillUnreadWikis.php. Like $wgEchoSectionTransition,
// this setting reduces performance but prevents glitches.
$wgEchoBundleTransition = false;

// The max number of notifications allowed for a user to do a live update,
// this is also the number of max notifications allowed for a user to have
// @FIXME - the name is not intuitive, probably change it when the deleteJob patch
// is deployed to both deployment branches
$wgEchoMaxUpdateCount = 2000;

// The max number of mention notifications allowed for a user to send at once
$wgEchoMaxMentionsCount = 50;

// Enable this when you want to enable mention failure and success notifications for the users.
$wgEchoMentionStatusNotifications = false;

// Disable this when you want to disable mentions for multiple section edits.
$wgEchoMentionsOnMultipleSectionEdits = true;

// Enable this to send out notifications for mentions on changes.
$wgEchoMentionOnChanges = true;

// The time interval between each bundle email in seconds
// set a small number for test wikis, should set this to 0 to disable email bundling
// if there is no delay queue support
$wgEchoBundleEmailInterval = 0;

// Whether or not to enable a new talk page message alert for logged in users
$wgEchoNewMsgAlert = true;

// Whether or not to show the footer feedback notice in the notifications popup
$wgEchoShowFooterNotice = false;

// A URL for the survey that appears in the footer feedback notice in the
// notification popup
$wgEchoFooterNoticeURL = '';

// Allowed notify types for all notifications and categories, unless overriden
// on a per-category or per-type basis.
// All of the keys from $wgEchoNotifiers must also be keys here.
$wgDefaultNotifyTypeAvailability = array(
	'web' => true,
	'email' => true,
);

// Define which notify types are available for each notification category
// If any notify types are omitted, it defaults to $wgDefaultNotifyTypeAvailability.
$wgNotifyTypeAvailabilityByCategory = array(
	// Otherwise, a user->user email could trigger an additional redundant
	// notification email.
	'emailuser' => array(
		'web' => true,
		'email' => false,
	),
	'mention-failure' => array(
		'web' => true,
		'email' => false
	),
	'mention-success' => array(
		'web' => true,
		'email' => false
	),
);

// Definitions of the different types of notification delivery that are possible.
// Each definition consists of a class name and a function name.
// See also: EchoNotificationController class.
$wgEchoNotifiers = array(
	'web' => array( 'EchoNotifier', 'notifyWithNotification' ), // web-based notification
	'email' => array( 'EchoNotifier', 'notifyWithEmail' ),
);

// List of usernames that will not trigger notification creation. This is initially
// for bots that perform automated edits that are not important enough to regularly
// spam people with notifications. Set to empty array when not in use.
$wgEchoAgentBlacklist = array();

// Page location of community maintained blacklist within NS_MEDIAWIKI.  Set to null to disable.
$wgEchoOnWikiBlacklist = 'Echo-blacklist';

// sprintf format of per-user notification agent whitelists. Set to null to disable.
$wgEchoPerUserWhitelistFormat = '%s/Echo-whitelist';

// Whether to enable the cross-wiki notifications feature. To enable this feature you need to:
// - have a global user system (e.g. CentralAuth or a shared user table)
// - have $wgMainStash and $wgMainWANCache shared between wikis
// - configure $wgEchoSharedTrackingDB
$wgEchoCrossWikiNotifications = false;

// Feature flag for the cross-wiki notifications beta feature
// If this is true, the cross-wiki notifications preference will appear in the BetaFeatures section;
// if this is false, it'll appear in the Notifications section instead.
// This does not control whether cross-wiki notifications are enabled by default. For that,
// use $wgDefaultUserOptions['echo-cross-wiki-notifications'] = true;
$wgEchoUseCrossWikiBetaFeature = false;

// Define the categories that notifications can belong to. Categories can be
// assigned the following parameters: priority, no-dismiss, tooltip, and usergroups.

// All parameters are optional.

// If a notifications type doesn't have a category parameter, it is
// automatically assigned to the 'other' category which is lowest priority and
// has no preferences or dismissibility.

// The priority parameter controls the order in which notifications are
// displayed in preferences and batch emails. Priority ranges from 1 to 10. If
// the priority is not specified, it defaults to 10, which is the lowest.

// The usergroups param specifies an array of usergroups eligible to recieve the
// notifications in the category. If no usergroups parameter is specified, all
// groups are eligible.

// The no-dismiss parameter disables the dismissability of notifications in the
// category. It can either be set to an array of notify types (see
// $wgEchoNotifiers) or an array containing 'all'.  If no-dismiss is 'all',
// it will not appear in preferences.
$wgEchoNotificationCategories = array(
	'system' => array(
		'priority' => 9,
		'no-dismiss' => array( 'all' ),
	),
	'user-rights' => array( // bug 55337
		'priority' => 9,
		'tooltip' => 'echo-pref-tooltip-user-rights',
	),
	'other' => array(
		'no-dismiss' => array( 'all' ),
	),
	'edit-user-talk' => array(
		'priority' => 1,
		'no-dismiss' => array( 'web' ),
		'tooltip' => 'echo-pref-tooltip-edit-user-talk',
	),
	'reverted' => array(
		'priority' => 9,
		'tooltip' => 'echo-pref-tooltip-reverted',
	),
	'article-linked' => array(
		'priority' => 5,
		'tooltip' => 'echo-pref-tooltip-article-linked',
	),
	'mention' => array(
		'priority' => 4,
		'tooltip' => 'echo-pref-tooltip-mention',
	),
	'mention-failure' => array(
		'priority' => 4,
		'tooltip' => 'echo-pref-tooltip-mention-failure',
	),
	'mention-success' => array(
		'priority' => 4,
		'tooltip' => 'echo-pref-tooltip-mention-success',
	),
	'emailuser' => array(
		'priority' => 9,
		'tooltip' => 'echo-pref-tooltip-emailuser',
	),
);

$echoIconPath = "Echo/modules/icons";

// Defines icons, which are 30x30 images. This is passed to BeforeCreateEchoEvent so
// extensions can define their own icons with the same structure.  It is recommended that
// extensions prefix their icon key. An example is myextension-name.  This will help
// avoid namespace conflicts.
// * You can use either a path or a url, but not both.
//   The value of 'path' is relative to $wgExtensionAssetsPath.
// * The value of 'url' should be a URL.
// * You should customize the site icon URL, which is:
//   $wgEchoNotificationIcons['site']['url']
$wgEchoNotificationIcons = array(
	'placeholder' => array(
		'path' => "$echoIconPath/generic.svg",
	),
	'trash' => array(
		'path' => "$echoIconPath/trash.svg",
	),
	'chat' => array(
		'path' => "$echoIconPath/chat.svg",
	),
	'edit' => array(
		'path' => array(
			'ltr' => "$echoIconPath/ooui-edit-ltr-progressive.svg",
			'rtl' => "$echoIconPath/ooui-edit-rtl-progressive.svg",
		),
	),
	'edit-user-talk' => array(
		'path' => "$echoIconPath/edit-user-talk.svg",
	),
	'linked' => array(
		'path' => "$echoIconPath/link-blue.svg",
	),
	'mention' => array(
		'path' => "$echoIconPath/mention.svg",
	),
	'mention-failure' => array(
		'path' => "$echoIconPath/mention-failure.svg",
	),
	'mention-success' => array(
		'path' => "$echoIconPath/mention-success.svg",
	),
	'mention-status-bundle' => array(
		'path' => "$echoIconPath/mention-status-bundle.svg",
	),
	'reviewed' => array(
		'path' => "$echoIconPath/reviewed.svg",
	),
	'revert' => array(
		'path' => "$echoIconPath/revert.svg",
	),
	'user-rights' => array(
		'path' => "$echoIconPath/user-rights.svg",
	),
	'emailuser' => array(
		'path' => "$echoIconPath/emailuser.svg",
	),
	'global' => array(
		'path' => "$echoIconPath/global.svg"
	),
	'site' => array(
		'url' => false
	),
);

// Definitions of the notification event types built into Echo.
// If formatter-class isn't specified, defaults to EchoBasicFormatter.

// 'notify-type-availabilty' - Defines which notifier (e.g. web/email) types are available
//   for each notification type (e.g. welcome).  Notification types are the keys of
//   $wgEchoNotificationCategories.

//   This is *ONLY* considered if the category is 'no-dismiss'.  Otherwise,
//   use $wgNotifyTypeAvailabilityByCategory

//   Without this constraint, we would have no way to display this information
//   on Special:Preferences in a non-misleading way.

//   If any notify types are omitted, it defaults to $wgNotifyTypeAvailabilityByCategory
//   which itself defaults to $wgDefaultNotifyTypeAvailability.
$wgEchoNotifications = array(
	'welcome' => array(
		EchoAttributeManager::ATTR_LOCATORS => array(
			'EchoUserLocator::locateEventAgent'
		),
		'category' => 'system',
		'group' => 'positive',
		'section' => 'message',
		// Only send web notification for welcome event
		'notify-type-availability' => array(
			'email' => false,
		),
		'presentation-model' => 'EchoWelcomePresentationModel',
	),
	'edit-user-talk' => array(
		'presentation-model' => 'EchoEditUserTalkPresentationModel',
		EchoAttributeManager::ATTR_LOCATORS => array(
			'EchoUserLocator::locateTalkPageOwner',
		),
		'category' => 'edit-user-talk',
		'group' => 'interactive',
		'section' => 'alert',
		'bundle' => array( 'web' => true, 'email' => false ),
		'immediate' => true,
	),
	'reverted' => array(
		'presentation-model' => 'EchoRevertedPresentationModel',
		EchoAttributeManager::ATTR_LOCATORS => array(
			array( 'EchoUserLocator::locateFromEventExtra', array( 'reverted-user-id' ) ),
		),
		'category' => 'reverted',
		'group' => 'negative',
		'section' => 'alert',
	),
	'page-linked' => array(
		'presentation-model' => 'EchoPageLinkedPresentationModel',
		EchoAttributeManager::ATTR_LOCATORS => array(
			'EchoUserLocator::locateArticleCreator',
		),
		'category' => 'article-linked',
		'group' => 'neutral',
		'section' => 'message',
		'bundle' => array( 'web' => true, 'email' => true, 'expandable' => true ),
	),
	'mention' => array(
		EchoAttributeManager::ATTR_LOCATORS => array(
			array( 'EchoUserLocator::locateFromEventExtra', array( 'mentioned-users' ) ),
		),
		'category' => 'mention',
		'group' => 'interactive',
		'section' => 'alert',
		'presentation-model' => 'EchoMentionPresentationModel',
	),
	'mention-failure' => array(
		EchoAttributeManager::ATTR_LOCATORS => array(
			array( 'EchoUserLocator::locateEventAgent' ),
		),
		'category' => 'mention-failure',
		'bundle' => array(
			'web' => true,
			'expandable' => true,
		),
		'group' => 'negative',
		'section' => 'alert',
		'presentation-model' => 'EchoMentionStatusPresentationModel',
	),
	'mention-failure-too-many' => array(
		EchoAttributeManager::ATTR_LOCATORS => array(
			array( 'EchoUserLocator::locateEventAgent' ),
		),
		'category' => 'mention-failure',
		'group' => 'negative',
		'section' => 'alert',
		'presentation-model' => 'EchoMentionStatusPresentationModel',
	),
	'mention-success' => array(
		EchoAttributeManager::ATTR_LOCATORS => array(
			array( 'EchoUserLocator::locateEventAgent' ),
		),
		'category' => 'mention-success',
		'bundle' => array(
			'web' => true,
			'expandable' => true,
		),
		'group' => 'positive',
		'section' => 'alert',
		'presentation-model' => 'EchoMentionStatusPresentationModel',
	),
	'user-rights' => array(
		EchoAttributeManager::ATTR_LOCATORS => array(
			array( 'EchoUserLocator::locateFromEventExtra', array( 'user' ) ),
		),
		'category' => 'user-rights',
		'group' => 'neutral',
		'section' => 'alert',
		'presentation-model' => 'EchoUserRightsPresentationModel',
	),
	'emailuser' => array(
		'presentation-model' => 'EchoEmailUserPresentationModel',
		EchoAttributeManager::ATTR_LOCATORS => array(
			array( 'EchoUserLocator::locateFromEventExtra', array( 'to-user-id' ) ),
		),
		'category' => 'emailuser',
		'group' => 'neutral',
		'section' => 'alert',
	),
	'foreign' => array(
		'presentation-model' => 'EchoForeignPresentationModel',
		EchoAttributeManager::ATTR_LOCATORS => array(
			'EchoUserLocator::locateEventAgent'
		),
		'category' => 'foreign',
		'group' => 'positive',
		'section' => 'alert',
	),
	'thank-you-edit' => array(
		'user-locators' => array(
			'EchoUserLocator::locateEventAgent'
		),
		'category' => 'system',
		// Only send 'web' notification
		'notify-type-availability' => array(
			'email' => false,
		),
		'group' => 'positive',
		'presentation-model' => 'EchoEditThresholdPresentationModel',
		'section' => 'message',
	),
);

// Enable new talk page messages alert for all logged in users by default
$wgDefaultUserOptions['echo-show-alert'] = true;

// By default, send emails for each notification as they come in
$wgDefaultUserOptions['echo-email-frequency'] = 0; /*EchoHooks::EMAIL_IMMEDIATELY*/

// By default, do not dismiss the special page invitation
$wgDefaultUserOptions['echo-dismiss-special-page-invitation' ] = 0;

if ( $wgAllowHTMLEmail ) {
	$wgDefaultUserOptions['echo-email-format'] = 'html'; /*EchoHooks::EMAIL_FORMAT_HTML*/
} else {
	$wgDefaultUserOptions['echo-email-format'] = 'plain-text'; /*EchoHooks::EMAIL_FORMAT_PLAIN_TEXT*/
}

// Set all of the events to notify by web but not email by default (won't affect events that don't email)
foreach ( $wgEchoNotificationCategories as $category => $categoryData ) {
	$wgDefaultUserOptions["echo-subscriptions-email-{$category}"] = false;
	$wgDefaultUserOptions["echo-subscriptions-web-{$category}"] = true;
}

// most settings default to web on, email off, but override these
$wgDefaultUserOptions['echo-subscriptions-email-system'] = true;
$wgDefaultUserOptions['echo-subscriptions-email-user-rights'] = true;
$wgDefaultUserOptions['echo-subscriptions-web-article-linked'] = false;
$wgDefaultUserOptions['echo-subscriptions-web-mention-failure'] = false;
$wgDefaultUserOptions['echo-subscriptions-web-mention-success'] = false;

// Echo Configuration for EventLogging
$wgEchoConfig = array(
	'version' => '1.12',
	'eventlogging' => array(
		/**
		 * Properties:
		 * - 'enabled': Whether it should be used
		 * - 'revision': revision id of the schema
		 * - 'client': whether the schema is needed client-side
		 */
		'Echo' => array(
			'enabled' => false,
			'revision' => 7731316,
			'client' => false,
		),
		'EchoMail' => array(
			'enabled' => false,
			'revision' => 5467650,
			'client' => false,
		),
		'EchoInteraction' => array(
			'enabled' => false,
			'revision' => 15823738,
			'client' => true,
		),
	)
);
