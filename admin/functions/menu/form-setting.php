<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

$form_id = strval( $this->get_form_id() );

$mail_tag = Mail_Tag::get_sn_mail_tag( $form_id );
$serial_number = Serial_Number::get_serial_number( $form_id );

$wpcf7_messages = Utility::get_wpcf7_contact_form_property(
	$form_id, 'messages'
);

// ========================================================
// カウント表示設定
// ========================================================

$attr_count = array(
	'size' => 5,
	'maxlength' => 5,
	'pattern' => _FORM_OPTIONS['11']['pattern'],
	'min' => 0,
	'max' => 99999,
);

$attr_daycount = array(
	'size' => 5,
	'maxlength' => 5,
	'pattern' => _FORM_OPTIONS['12']['pattern'],
	'min' => 0,
	'max' => 99999,
);
if ( !NT_WPCF7SN::is_working_dayreset() ) {
	$attr_daycount += array(
		'readonly' => 'readonly',
	);
}

// ========================================================
// オプション表示設定
// ========================================================

$list_type = array(
	0 => __( 'Serial Number', _TEXT_DOMAIN ),
	1 => __( 'Timestamp ( UNIX time )', _TEXT_DOMAIN ),
	2 => __( 'Timestamp ( Date )', _TEXT_DOMAIN ),
	3 => __( 'Timestamp ( Date + Time )', _TEXT_DOMAIN ),
	4 => __( 'Unique ID', _TEXT_DOMAIN ),
);

$attr_prefix = array(
	'size' => 30,
	'pattern' => _FORM_OPTIONS['04']['pattern'],
);

$attr_digits = array(
	'size' => 1,
	'maxlength' => 1,
	'pattern' => _FORM_OPTIONS['05']['pattern'],
	'min' => 1,
	'max' => 9,
);

$attr_dayreset = [];
if ( !NT_WPCF7SN::is_working_dayreset() ) {
	$attr_dayreset += array(
		'disabled' => 'disabled',
	);
}

// ========================================================
// 高度な設定
// ========================================================

$list_unix_format = array(
	0 => __( 'seconds (s)', _TEXT_DOMAIN ),
	1 => __( 'milliseconds (ms)', _TEXT_DOMAIN ),
	2 => __( 'microseconds (μs)', _TEXT_DOMAIN ),
);

$attr_sent_msg = array(
	'pattern' => _FORM_OPTIONS['15']['pattern'],
	'placeholder' => _FORM_OPTIONS['15']['default'],
);

// HTML表示 ================================================================ ?>

<p><?php $this->hidden( 'form_id', $form_id ); ?></p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<h3><i class="fa-solid fa-code fa-fw"></i><?php _e( 'Mail-Tag', _TEXT_DOMAIN ); ?></h3>

<p>
	<?php $this->copy_text(
		'mail_tag',
		[], '',
		$mail_tag
	); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<h3><i class="fa-solid fa-stopwatch-20 fa-fw"></i><?php _e( 'Mail Counter', _TEXT_DOMAIN ); ?></h3>

<p class="<?php if ( 'yes' === $GLOBALS['_NT_WPCF7SN'][$form_id]['10'] ) { ?> hidden <?php } ?>">
	<?php _e( 'Current Count', _TEXT_DOMAIN ); ?>

	<?php $this->number(
		_FORM_OPTIONS['11']['key'],
		$attr_count, '',
		_FORM_OPTIONS['11']['default']
	); ?>

	( <?php _e( 'Up to 5 digits integer : 0~99999', _TEXT_DOMAIN ); ?> )
</p>

<p class="<?php if ( 'yes' !== $GLOBALS['_NT_WPCF7SN'][$form_id]['10'] ) { ?> hidden <?php } ?>">
	<?php _e( 'Daily Count', _TEXT_DOMAIN ); ?>

	<?php $this->number(
		_FORM_OPTIONS['12']['key'],
		$attr_daycount, '',
		_FORM_OPTIONS['12']['default']
	); ?>

	( <?php _e( 'Up to 5 digits integer : 0~99999', _TEXT_DOMAIN ); ?> )<br/>
	<?php _e( '* Reset on date change', _TEXT_DOMAIN ); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<p><?php $this->submit( __( 'Change', _TEXT_DOMAIN ) ); ?></p>

<p class="example">
	<?php $this->view_html( sprintf( ''
		. __( 'Display Example', _TEXT_DOMAIN )
		. ' [ %s ]'
		, $serial_number
	) ); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<h3><i class="fa-solid fa-sliders fa-fw"></i><?php _e( 'Display Settings', _TEXT_DOMAIN ); ?></h3>

<h4><?php _e( 'Number Type', _TEXT_DOMAIN ); ?></h4>

<p>
	<?php $this->radio(
		_FORM_OPTIONS['03']['key'],
		$list_type, true,
		_FORM_OPTIONS['03']['default']
	); ?>
</p>

<h4><?php _e( 'Display Options', _TEXT_DOMAIN ); ?></h4>

<p>
	<?php _e( 'Prefix', _TEXT_DOMAIN ); ?>

	<?php $this->text(
		_FORM_OPTIONS['04']['key'],
		$attr_prefix, '',
		_FORM_OPTIONS['04']['default']
	); ?>

	( <?php _e( 'Non-whitespace characters', _TEXT_DOMAIN ); ?> )
</p>

<p>
	<?php _e( 'Counter digits', _TEXT_DOMAIN ); ?>

	<?php $this->number(
		_FORM_OPTIONS['05']['key'],
		$attr_digits, '',
		_FORM_OPTIONS['05']['default']
	); ?>

	( <?php _e( '1 digit integer : 1~9', _TEXT_DOMAIN ); ?> )
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['06']['key'],
		__( 'Display the delimiter "-".', _TEXT_DOMAIN ),
		[],
		_FORM_OPTIONS['06']['default']
	); ?>
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['07']['key'],
		__( 'Omit the number of years to 2 digits.', _TEXT_DOMAIN ),
		[],
		_FORM_OPTIONS['07']['default']
	); ?>
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['08']['key'],
		__( 'Don\'t display mail count.', _TEXT_DOMAIN )
		. ' ( ' . __( 'UNIX time & Unique ID', _TEXT_DOMAIN ) . ' )',
		[],
		_FORM_OPTIONS['08']['default']
	); ?>
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['10']['key'],
		__( 'Use the daily reset mail counter.', _TEXT_DOMAIN ),
		$attr_dayreset,
		_FORM_OPTIONS['10']['default']
	); ?>
	<?php if ( !NT_WPCF7SN::is_working_dayreset() ) { ?><br/>
	<span style="color:#fa514b;">
		<?php _e( '* This feature does not work with your PHP version. ( PHP >= 5.2.0 )', _TEXT_DOMAIN ); ?>
	</span>
	<?php } ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<p><?php $this->submit( __( 'Settings', _TEXT_DOMAIN ) ); ?></p>

<p class="example">
	<?php $this->view_html( sprintf( ''
		. __( 'Display Example', _TEXT_DOMAIN )
		. ' [ %s ]'
		, $serial_number
	) ); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<h3><i class="fa-solid fa-gears fa-fw"></i><?php _e( 'Advanced Settings', _TEXT_DOMAIN ); ?></h3>

<h4><?php _e( 'UNIX Time Display Format', _TEXT_DOMAIN ); ?></h4>

<p>
	<?php $this->radio(
		_FORM_OPTIONS['09']['key'],
		$list_unix_format, false,
		_FORM_OPTIONS['09']['default']
	); ?>
</p>

<h4><?php _e( 'Disable Mail Count Increase', _TEXT_DOMAIN ); ?></h4>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['13']['key'],
		__( 'Don\'t increment count when mail send fails.', _TEXT_DOMAIN ),
		[],
		_FORM_OPTIONS['13']['default']
	); ?>
</p>

<h4><?php _e( 'Edit Send Result Messages', _TEXT_DOMAIN ); ?></h4>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['14']['key'],
		__( 'Don\'t add the serial number display to the send result message.', _TEXT_DOMAIN ),
		[],
		_FORM_OPTIONS['14']['default']
	); ?>
</p>

<p>
	<?php _e( 'Message was sent successfully :', _TEXT_DOMAIN ); ?><br/>

	<?php $this->text(
		_FORM_OPTIONS['15']['key'],
		$attr_sent_msg, '100',
		_FORM_OPTIONS['15']['default']
	); ?>
</p>

<p>
	<?php _e( '* Appended to the end of messages displayed by the Contact Form 7.', _TEXT_DOMAIN ); ?>
</p>

<p class="example">
	<?php _e( 'Display Example', _TEXT_DOMAIN ); ?> :<br>

	<?php if ( !is_null( $wpcf7_messages ) ) {
		$this->view_html( $wpcf7_messages['mail_sent_ok'] );
	} ?>

	<?php $this->view_html( sprintf( '( %s%s%s )'
		, $GLOBALS['_NT_WPCF7SN'][$form_id]['15']
		, empty( $GLOBALS['_NT_WPCF7SN'][$form_id]['15'] ) ? '' : ' '
		, $serial_number
	) ); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<p><?php $this->submit( __( 'Settings', _TEXT_DOMAIN ) ); ?></p>

<p class="example">
	<?php $this->view_html( sprintf( ''
		. __( 'Display Example', _TEXT_DOMAIN )
		. ' [ %s ]'
		, $serial_number
	) ); ?>
</p>

<?php // ======================================================================
