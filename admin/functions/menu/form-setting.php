<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

$form_id = $this->get_form_id();

// ========================================================
// メールタグ表示設定
// ========================================================

$attr_mail_tag = array(
	'readonly' => 'readonly',
	'onfocus' => 'this.select();',
);

$mail_tag = sprintf( '[%s%s]'
	, _MAIL_TAG_PREFIX
	, strval( $form_id )
);

// ========================================================
// カウント表示設定
// ========================================================

$attr_count = array(
	'size' => 5,
	'maxlength' => 5,
	'pattern' => _FORM_OPTIONS['count']['pattern'],
	'min' => 0,
	'max' => 99999,
);

$attr_daycount = array(
	'size' => 5,
	'maxlength' => 5,
	'pattern' => _FORM_OPTIONS['daycount']['pattern'],
	'min' => 0,
	'max' => 99999,
);

// ========================================================
// オプション表示設定
// ========================================================

$list_type = array(
	0 => __( 'Serial Number', _TEXT_DOMAIN ),
	1 => __( 'Timestamp (UnixTime)', _TEXT_DOMAIN ),
	2 => __( 'Timestamp (Date)', _TEXT_DOMAIN ),
	3 => __( 'Timestamp (Date + Time)', _TEXT_DOMAIN ),
	4 => __( 'Unique ID', _TEXT_DOMAIN ),
);

$attr_prefix = array(
	'size' => 15,
	'maxlength' => 10,
	'pattern' => _FORM_OPTIONS['prefix']['pattern'],
);

$attr_digits = array(
	'size' => 1,
	'maxlength' => 1,
	'pattern' => _FORM_OPTIONS['digits']['pattern'],
	'min' => 1,
	'max' => 9,
);

// HTML表示 ================================================================ ?>

<p><?php $this->hidden( 'form_id', $form_id ); ?></p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<h3><i class="fa-solid fa-code fa-fw"></i><?php _e( 'Mail-Tag', _TEXT_DOMAIN ); ?></h3>

<p>
	<?php $this->text(
		'mail_tag',
		$attr_mail_tag, '',
		$mail_tag
	); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<h3><i class="fa-solid fa-stopwatch-20 fa-fw"></i><?php _e( 'Count', _TEXT_DOMAIN ); ?></h3>

<p>
	<?php _e( 'Current Count', _TEXT_DOMAIN ); ?>

	<?php $this->number(
		_FORM_OPTIONS['count']['key'],
		$attr_count, '',
		_FORM_OPTIONS['count']['default']
	); ?>

	( <?php _e( 'Up to 5 digits integer. 0~99999', _TEXT_DOMAIN ); ?> )
</p>

<p>
	<?php _e( 'Daily Count', _TEXT_DOMAIN ); ?>

	<?php $this->number(
		_FORM_OPTIONS['daycount']['key'],
		$attr_daycount, '',
		_FORM_OPTIONS['daycount']['default']
	); ?>

	( <?php _e( 'Up to 5 digits integer. 0~99999', _TEXT_DOMAIN ); ?> )<br/>
	<?php _e( '* Reset on date change', _TEXT_DOMAIN ); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<p><?php $this->submit( __( 'Change', _TEXT_DOMAIN ) ); ?></p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<h3><i class="fa-solid fa-sliders fa-fw"></i><?php _e( 'Setting', _TEXT_DOMAIN ); ?></h3>

<h4><?php _e( 'Type', _TEXT_DOMAIN ); ?></h4>

<p>
	<?php $this->radio(
		_FORM_OPTIONS['type']['key'],
		$list_type, true,
		_FORM_OPTIONS['type']['default']
	); ?>
</p>

<h4><?php _e( 'Options', _TEXT_DOMAIN ); ?></h4>

<p>
	<?php _e( 'Prefix', _TEXT_DOMAIN ); ?>

	<?php $this->text(
		_FORM_OPTIONS['prefix']['key'],
		$attr_prefix, '',
		_FORM_OPTIONS['prefix']['default']
	); ?>

	( <?php _e( 'Within 10 characters. Unusable \\"&\'<>', _TEXT_DOMAIN ); ?> )
</p>

<p>
	<?php _e( 'Digits', _TEXT_DOMAIN ); ?>

	<?php $this->number(
		_FORM_OPTIONS['digits']['key'],
		$attr_digits, '',
		_FORM_OPTIONS['digits']['default']
	); ?>

	( <?php _e( '1 digit integer. 1~9', _TEXT_DOMAIN ); ?> )
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['separator']['key'],
		__( 'Display the delimiter "-".', _TEXT_DOMAIN ),
		_FORM_OPTIONS['separator']['default']
	); ?>
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['year2dig']['key'],
		__( 'Omit the number of years to 2 digits.', _TEXT_DOMAIN ),
		_FORM_OPTIONS['year2dig']['default']
	); ?>
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['nocount']['key'],
		__( 'Don\'t display count with unique ID.', _TEXT_DOMAIN ),
		_FORM_OPTIONS['nocount']['default']
	); ?>
</p>

<p>
	<?php $this->checkbox(
		_FORM_OPTIONS['dayreset']['key'],
		__( 'Use the daily reset counter.', _TEXT_DOMAIN ),
		_FORM_OPTIONS['dayreset']['default']
	); ?>
</p>

<?php // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ?>

<p><?php $this->submit( __( 'Settings', _TEXT_DOMAIN ) ); ?></p>

<?php // ======================================================================
