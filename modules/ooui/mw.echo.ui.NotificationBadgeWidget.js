( function ( mw, $ ) {
	/**
	 * Notification badge button widget for echo popup.
	 *
	 * @class
	 * @extends OO.ui.ButtonWidget
	 *
	 * @constructor
	 * @param {Object} [config] Configuration object
	 * @cfg {string} [type='alert'] Notification type 'alert' or 'message'
	 * @cfg {number} [numItems=0] How many items are in the button display
	 * @cfg {boolean} [hasUnread=false] Whether there are unread items
	 * @cfg {boolean} [markReadWhenSeen=false] Mark all notifications as read on open
	 * @cfg {string|Object} [badgeIcon] The icons to use for this button.
	 *  If this is a string, it will be used as the icon regardless of the state.
	 *  If it is an object, it must include
	 *  the properties 'unread' and 'read' with icons attached to both. For example:
	 *  { badgeIcon: {
	 *    unread: 'bellOn',
	 *    read: 'bell'
	 *  } }
	 */
	mw.echo.ui.NotificationBadgeWidget = function MwEchoUiNotificationBadgeButtonPopupWidget( config ) {
		var buttonFlags, allNotificationsButton, preferencesButton, $footer;

		config = config || {};
		config.links = config.links || {};

		// Mixin constructors
		OO.ui.mixin.PendingElement.call( this, config );

		this.type = config.type || 'alert';
		this.numItems = config.numItems || 0;
		this.hasUnread = !!config.hasUnread;
		this.badgeIcon = config.badgeIcon || {};
		this.markReadWhenSeen = !!config.markReadWhenSeen;

		this.hasRunFirstTime = false;

		buttonFlags = [ 'primary' ];
		if ( this.hasUnread ) {
			buttonFlags.push( 'unseen' );
		}

		// View model
		this.notificationsModel = new mw.echo.dm.NotificationsModel( {
			type: this.type,
			limit: 25,
			userLang: mw.config.get( 'wgUserLanguage' )
		} );

		// Notifications widget
		this.notificationsWidget = new mw.echo.ui.NotificationsWidget(
			this.notificationsModel,
			{
				type: this.type,
				markReadWhenSeen: this.markReadWhenSeen
			}
		);

		this.setPendingElement( this.notificationsWidget.$element );

		// Footer
		allNotificationsButton = new OO.ui.ButtonWidget( {
			framed: false,
			icon: 'next',
			label: mw.msg( 'echo-overlay-link' ),
			href: config.links.notifications,
			classes: [ 'mw-echo-ui-notificationBadgeButtonPopupWidget-footer-allnotifs' ]
		} );

		preferencesButton = new OO.ui.ButtonWidget( {
			framed: false,
			icon: 'advanced',
			label: mw.msg( 'mypreferences' ),
			href: config.links.preferences,
			classes: [ 'mw-echo-ui-notificationBadgeButtonPopupWidget-footer-preferences' ]
		} );

		$footer = $( '<div>' )
			.addClass( 'mw-echo-ui-notificationBadgeButtonPopupWidget-footer' )
			.append(
				allNotificationsButton.$element,
				preferencesButton.$element
			);

		// Parent constructor
		mw.echo.ui.NotificationBadgeWidget.parent.call( this, $.extend( {
			framed: false,
			flags: buttonFlags,
			label: this.numItems,
			icon: (
				typeof this.badgeIcon === 'string' ?
				this.badgeIcon :
				this.badgeIcon[ this.hasUnread ? 'unread' : 'read' ]
			),
			popup: {
				$content: this.notificationsWidget.$element,
				$footer: $footer,
				width: 450,
				head: true,
				// This covers the messages 'echo-notification-alert-text-only'
				// and 'echo-notification-message-text-only'
				label: mw.msg( 'echo-notification-' + this.type + '-text-only' )
			}
		}, config ) );

		// HACK: Add an icon to the popup head label
		this.popup.$head.prepend( new OO.ui.IconWidget( { icon: 'bell' } ).$element );

		// Mark all as read button
		this.markAllReadButton = new OO.ui.ButtonWidget( {
			framed: false,
			label: mw.msg( 'echo-mark-all-as-read' ),
			classes: [ 'mw-echo-ui-notificationsWidget-markAllReadButton' ]
		} );

		// Hide the close button
		this.popup.closeButton.toggle( false );
		// Add the 'mark all as read' button to the header
		this.popup.$head.append( this.markAllReadButton.$element );
		this.markAllReadButton.toggle( !this.markReadWhenSeen && this.hasUnread );

		// Events
		this.markAllReadButton.connect( this, { click: 'onMarkAllReadButtonClick' } );
		this.notificationsModel.connect( this, {
			updateSeenTime: 'updateBadge',
			add: 'updateBadge',
			unseenChange: 'updateBadge',
			unreadChange: 'updateBadge'
		} );

		this.$element
			.addClass(
				'mw-echo-ui-notificationBadgeButtonPopupWidget ' +
				'mw-echo-ui-notificationBadgeButtonPopupWidget-' + this.type
			);
	};

	/* Initialization */

	OO.inheritClass( mw.echo.ui.NotificationBadgeWidget, OO.ui.PopupButtonWidget );
	OO.mixinClass( mw.echo.ui.NotificationBadgeWidget, OO.ui.mixin.PendingElement );

	/**
	 * Update the badge state and label based on changes to the model
	 */
	mw.echo.ui.NotificationBadgeWidget.prototype.updateBadge = function () {
		var unseenCount = this.notificationsModel.getUnseenCount(),
			unreadCount = this.notificationsModel.getUnreadCount();

		// Update numbers and seen/unseen state
		this.setFlags( { unseen: !!unseenCount } );
		this.setLabel( String( unreadCount ) );
	};

	/**
	 * Respond to 'mark all as read' button click
	 */
	mw.echo.ui.NotificationBadgeWidget.prototype.onMarkAllReadButtonClick = function () {
		this.notificationsModel.markAllRead();
	};

	/**
	 * Extend the response to button click so we can also update the notification list.
	 */
	mw.echo.ui.NotificationBadgeWidget.prototype.onAction = function () {
		var widget = this,
			time = mw.now();

		// Parent method
		mw.echo.ui.NotificationBadgeWidget.parent.prototype.onAction.call( this, arguments );

		// Log the click event
		mw.echo.logger.logInteraction(
			'ui-badge-link-click',
			mw.echo.Logger.static.context,
			null,
			this.type
		);

		if ( !this.notificationsModel.isFetchingNotifications() ) {
			if ( this.hasRunFirstTime ) {
				// Don't clear items on the first time we open the popup
				this.notificationsModel.clearItems();

				// HACK: Clippable doesn't resize the clippable area when
				// it calculates the new size. Since the popup contents changed
				// and the popup is "empty" now, we need to manually set its
				// size to 1px so the clip calculations will resize it properly.
				// See bug report: https://phabricator.wikimedia.org/T110759
				this.popup.$clippable.css( 'height', '1px' );
				this.popup.clip();
			}

			this.pushPending();
			this.notificationsModel.fetchNotifications()
				.then( function ( idArray ) {
					// Clip again
					widget.popup.clip();

					// Log impressions
					mw.echo.logger.logNotificationImpressions( this.type, idArray, mw.echo.Logger.static.context.popup );

					// Log timing
					mw.track( 'timing.MediaWiki.echo.overlay', mw.now() - time );

					// // Mark notifications as 'read' if markReadWhenSeen is set to true
					if ( widget.markReadWhenSeen ) {
						return widget.notificationsModel.markAllRead();
					}
				} )
				.then( function () {
					// Update seen time
					widget.notificationsModel.updateSeenTime();
				} )
				.always( function () {
					// Pop pending
					widget.popPending();
					// Nullify the promise; let the user fetch again
					widget.fetchNotificationsPromise = null;
				} );

			this.hasRunFirstTime = true;
		}
	};

	/**
	 * Get the notifications model attached to this widget
	 *
	 * @return {mw.echo.dm.NotificationsModel} Notifications model
	 */
	mw.echo.ui.NotificationBadgeWidget.prototype.getModel = function () {
		return this.notificationsModel;
	};

} )( mediaWiki, jQuery );
