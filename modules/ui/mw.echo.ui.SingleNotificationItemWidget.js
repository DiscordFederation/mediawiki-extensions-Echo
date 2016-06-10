( function ( mw ) {
	/**
	 * Single notification item widget for echo popup.
	 *
	 * @class
	 * @extends mw.echo.ui.NotificationItemWidget
	 * @mixins OO.ui.mixin.PendingElement
	 *
	 * @constructor
	 * @param {mw.echo.Controller} controller Echo notifications controller
	 * @param {mw.echo.dm.NotificationItem} model Notification item model
	 * @param {Object} [config] Configuration object
	 * @cfg {jQuery} [$overlay] A jQuery element functioning as an overlay
	 *  for popups.
	 * @cfg {boolean} [bundle=false] This notification is part of a bundle
	 */
	mw.echo.ui.SingleNotificationItemWidget = function MwEchoUiSingleNotificationItemWidget( controller, model, config ) {
		config = config || {};

		// Parent constructor
		mw.echo.ui.SingleNotificationItemWidget.parent.call( this, controller, model, config );
		// Mixin constructors
		OO.ui.mixin.PendingElement.call( this, config );

		this.controller = controller;
		this.model = model;

		this.bundle = !!config.bundle;
		this.$overlay = config.$overlay || this.$element;

		// Toggle 'mark as read' functionality
		this.toggleMarkAsReadButtons( !this.model.isRead() );

		// Events
		this.model.connect( this, { update: 'updateDataFromModel' } );

		// Update read and seen states from the model
		this.updateDataFromModel();
	};

	/* Initialization */

	OO.inheritClass( mw.echo.ui.SingleNotificationItemWidget, mw.echo.ui.NotificationItemWidget );
	OO.mixinClass( mw.echo.ui.SingleNotificationItemWidget, OO.ui.mixin.PendingElement );

	/* Methods */
	mw.echo.ui.SingleNotificationItemWidget.prototype.onPrimaryLinkClick = function () {
		// Log notification click

		mw.echo.logger.logInteraction(
			mw.echo.Logger.static.actions.notificationClick,
			mw.echo.Logger.static.context.popup,
			this.getModel().getId(),
			this.getModel().getCategory(),
			false,
			// Source of this notification if it is cross-wiki
			// TODO: For notifications in local bundles, we need
			// to consider changing this
			this.bundle ? this.getModel().getSource() : ''
		);
	};

	/**
	 * @inheritdoc
	 */
	mw.echo.ui.SingleNotificationItemWidget.prototype.markRead = function ( isRead ) {
		isRead = isRead !== undefined ? isRead : true;

		this.controller.markSingleItemRead(
			this.model.getId(),
			this.model.getModelName(),
			this.model.isForeign(),
			!!isRead
		);
	};

	/**
	 * Update item state when the item model changes.
	 *
	 * @fires sortChange
	 */
	mw.echo.ui.SingleNotificationItemWidget.prototype.updateDataFromModel = function () {
		this.toggleRead( this.model.isRead() );
		this.toggleSeen( this.model.isSeen() );

		// Emit 'sortChange' so the SortedList can update this
		// item's place in the list
		this.emit( 'sortChange' );
	};
} )( mediaWiki );
