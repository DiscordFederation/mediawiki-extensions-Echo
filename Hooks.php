<?php

class EchoHooks {
	public static function getSchemaUpdates($updater) {
		$dir = dirname(__FILE__);
		$baseSQLFile = "$dir/echo.sql";
		$updater->addExtensionTable( 'echo_subscription', $baseSQLFile );
		$updater->addExtensionTable( 'echo_event', $baseSQLFile );
		$updater->addExtensionTable( 'echo_notification', $baseSQLFile );

		$updater->modifyField( 'echo_event', 'event_agent',
			"$dir/db_patches/patch-event_agent-split.sql", true );
		$updater->modifyField( 'echo_event', 'event_variant',
			"$dir/db_patches/patch-event_variant_nullability.sql", true );
		$updater->modifyField( 'echo_event', 'event_extra',
			"$dir/db_patches/patch-event_extra-size.sql", true );
		return true;
	}

	public static function getDefaultNotifiedUsers($event, &$users) {
		switch( $event->getType() ) {
			case 'edit-user-talk':
				if ( !$event->getTitle() || !$event->getTitle()->getNamespace() == NS_USER_TALK ) {
					break;
				}

				$username = $event->getTitle()->getText();
				$user = User::newFromName( $username );
				if ($user->getId()) {
					$user = User::newFromName($username);
					$users[$user->getId()] = $user;
				}
			break;
			case 'add-comment':
			case 'add-talkpage-topic':
				$extraData = $event->getExtra();

				if ( !isset( $extraData['revid'] ) || !$extraData['revid'] ) {
					break;
				}

				$revision = Revision::newFromId( $extraData['revid'] );

				$users = array_merge(
					$users,
					EchoDiscussionParser::getNotifiedUsersForComment($revision)
				);
			break;
		}

		return true;
	}

	public static function getNotificationTypes( $subscription, $event, &$notifyTypes ) {
		$type = $event->getType();
		$user = $subscription->getUser();

		// Figure out when to disallow email notifications
		if ( $type == 'edit' ) {
			if ( ! $user->getOption('enotifwatchlistpages') ) {
				$notifyTypes = array_diff( $notifyTypes, array('email') );
			}
		} elseif ( $type == 'edit-user-talk' ) {
			if ( ! $user->getOption('enotifusertalkpages') ) {
				$notifyTypes = array_diff( $notifyTypes, array('email') );
			}
		}

		if ( ! $user->getOption('enotifminoredits') ) {
			$extra = $event->getExtra();
			if ( !empty($extra['revid']) ) {
				$rev = Revision::newFromID($extra['revid']);

				if ( $rev->isMinor() ) {
					$notifyTypes = array_diff( $notifyTypes, array('email') );
				}
			}
		}
		return true;
	}

	public static function getPreferences( $user, &$preferences ) {
		$preferences['echo-notify-watchlist'] = array(
			'type' => 'toggle',
			'label-message' => 'echo-pref-notify-watchlist',
			'section' => 'echo',
		);
		return true;
	}

	public static function onWatch( $user, $article ) {
		if ( ! $user->getOption('echo-notify-watchlist') ) {
			return true;
		}

		$subscription = new EchoSubscription( $user, 'edit', $article->getTitle() );
		$subscription->enableNotification('notify');
		$subscription->save();
		return true;
	}

	public static function onUnwatch( $user, $article ) {
		$subscription = new EchoSubscription( $user, 'edit', $article->getTitle() );
		$subscription->disableNotification('notify');
		$subscription->save();
		return true;
	}

	public static function onArticleSaved( &$article, &$user, $text, $summary, $minoredit, $watchthis, $sectionanchor, &$flags, $revision, &$status ) {	
		if ( !$revision ) {
			return true;
		}

		$event = EchoEvent::create( array(
			'type' => 'edit',
			'title' => $article->getTitle(),
			'extra' => array('revid' => $revision->getID()),
			'agent' => $user,
		) );


		if ( $article->getTitle()->isTalkPage() ) {
			EchoDiscussionParser::generateEventsForRevision( $revision );
		}

		return true;
	}

	static function beforePageDisplay( $out, $skin ) {
		global $wgUser;
		if ( !$wgUser->isAnon() ) {
			$out->addModules( array('ext.echo.overlay') );
		}
		return true;
	}

	static function onPersonalUrls( &$personal_urls, &$title ) {
		global $wgUser, $wgLang, $wgOut;

		if ( $wgUser->isAnon() ) {
			return true;
		}

		$notificationCount = EchoNotificationController::getNotificationCount( $wgUser );

		$msg = wfMessage( $notificationCount == 0 ? 'echo-link-none' : 'echo-link-new' );
		$url = SpecialPage::getTitleFor( 'Notifications' )->getLocalURL();

		$notificationsLink = array(
			'href' => $url,
			'text' => $msg->params($wgLang->formatNum( $notificationCount ) )->text(),
			'active' => $notificationCount > 0,
		);

		$insertUrls = array( 'notifications' => $notificationsLink );
		$personal_urls = wfArrayInsertAfter( $personal_urls, $insertUrls, 'watchlist' );

		return true;
	}

	static function abortEmailNotification() {
		return false;
	}

	public static function makeGlobalVariablesScript( &$vars, $outputPage ) {
		$user = $outputPage->getUser();

		if ( ! $user->isAnon() ) {
			$vars['wgEchoOverlayConfiguration'] = array(
				'timestamp' => wfTimestamp( TS_UNIX, wfTimestampNow() ),
				'notification-count' => EchoNotificationController::getNotificationCount( $user ),
			);
		}

		return true;
	}

	static function getUnitTests( &$files ) {
		$dir = dirname( __FILE__ ) . '/tests';
		$files[] = "$dir/DiscussionParserTest.php";
	}
}
