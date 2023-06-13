<?php
if ( !defined( 'ABSPATH' ) ) exit;

// HTML表示 ================================================================ ?>

	<?php // ===== チェックボックス ===== ?>

	<h3><i class="fa-regular fa-square-check fa-fw"></i>チェックボックス</h3>
	
	<p><?php $this->checkbox( 'checkbox_1', 'チェックボックス1' ); ?></p>
	<p><?php $this->checkbox( 'checkbox_2', 'チェックボックス2' ); ?></p>
	<p>
		<?php $this->checkbox( 'checkbox_3', 'チェックボックス3' ); ?>
		<?php $this->checkbox( 'checkbox_4', 'チェックボックス4' ); ?>
		<?php $this->checkbox( 'checkbox_5', 'チェックボックス5' ); ?>
		<?php $this->checkbox( 'checkbox_6', 'チェックボックス6' ); ?>
	</p>

	<?php // ===== スイッチ ===== ?>

	<h3><i class="fa-solid fa-toggle-off fa-fw"></i>スイッチ</h3>
	
	<p><?php $this->switch( 'switch_1', 'スイッチ1' ); ?></p>
	<p><?php $this->switch( 'switch_2', 'スイッチ2' ); ?></p>
	<p>
		<?php $this->switch( 'switch_3', 'スイッチ3' ); ?>
		<?php $this->switch( 'switch_4', 'スイッチ4' ); ?>
		<?php $this->switch( 'switch_5', 'スイッチ5' ); ?>
		<?php $this->switch( 'switch_6', 'スイッチ6' ); ?>
	</p>

	<?php // ===== 数値フィールド ===== ?>

	<h3><i class="fa-regular fa-keyboard fa-fw"></i>数値フィールド</h3>

	<?php $attr = array( 'placeholder' => '数値' ); ?>

	<p>
		数値フィールド : 
		<?php $this->number( 'number_1', $attr ); ?>
		※数値フィールド
	</p>

	<p>size '    ' :<br/><?php $this->number( 'number_2', [], '' ); ?></p>
	<p>size ' 15%' :<br/><?php $this->number( 'number_3', [], 15 ); ?></p>
	<p>size ' 30%' :<br/><?php $this->number( 'number_4', [], 30 ); ?></p>
	<p>size ' 50%' :<br/><?php $this->number( 'number_5', [], 50 ); ?></p>
	<p>size '100%' :<br/><?php $this->number( 'number_6', [], 100 ); ?></p>

	<?php // ===== テキストフィールド ===== ?>

	<h3><i class="fa-regular fa-keyboard fa-fw"></i>テキストフィールド</h3>

	<?php $attr = array( 'placeholder' => 'テキストフィールド' ); ?>

	<p>
		テキストフィールド : 
		<?php $this->text( 'text_1', $attr ); ?>
		※テキストフィールド
	</p>

	<p>size '    ' :<br/><?php $this->text( 'text_2', [], '' ); ?></p>
	<p>size ' 15%' :<br/><?php $this->text( 'text_3', [], 15 ); ?></p>
	<p>size ' 30%' :<br/><?php $this->text( 'text_4', [], 30 ); ?></p>
	<p>size ' 50%' :<br/><?php $this->text( 'text_5', [], 50 ); ?></p>
	<p>size '100%' :<br/><?php $this->text( 'text_6', [], 100 ); ?></p>

	<?php // ===== テキストエリア ===== ?>

	<h3><i class="fa-regular fa-keyboard fa-fw"></i>テキストエリア</h3>

	<?php $attr = array( 'placeholder' => 'テキストエリア', 'rows' => '4' ); ?>

	<p><?php $this->textarea( 'textarea_1', $attr ); ?></p>
	<p><?php $this->textarea( 'textarea_2', [ 'required' => 'required' ], 100 ); ?></p>

	<?php // ===== ラジオボタン ===== ?>

	<h3><i class="fa-solid fa-list-ul fa-fw"></i>ラジオボタン</h3>

	<?php $list = [ 1 => 'リスト1', 2 => 'リスト2', 3 => 'リスト3' ]; ?>
	<p><?php $this->radio( 'radio_1', $list ); ?></p>
	<p><?php $this->radio( 'radio_2', $list, false ); ?></p>

	<?php // ===== セレクトボックス ===== ?>

	<h3><i class="fa-regular fa-rectangle-list fa-fw"></i>セレクトボックス</h3>

	<?php $list = [ 1 => 'セレクト1', 2 => 'セレクト2', 3 => 'セレクト3' ]; ?>
	<p><?php $this->select( 'select_1', $list ); ?></p>

	<?php // ===== 非表示フィールド ===== ?>

	<h3><i class="fa-regular fa-keyboard fa-fw"></i>非表示フィールド</h3>

	<p>
		非表示フィールド : 
		<?php $this->hidden( 'hidden_1', '非表示テキスト' ); ?>
	</p>

	<?php // ===== コピーテキストフィールド ===== ?>

	<h3><i class="fa-regular fa-keyboard fa-fw"></i>コピーテキストフィールド</h3>

	<?php $attr = array( 'placeholder' => 'コピーテキスト' ); ?>

	<p>
		コピーテキストフィールド : 
		<?php $this->copy_text( 'copy_text_1', $attr ); ?>
		※コピーテキストフィールド
	</p>

	<p>size '    ' :<br/><?php $this->copy_text( 'copy_text_2', [], '' ); ?></p>
	<p>size ' 15%' :<br/><?php $this->copy_text( 'copy_text_3', [], 15 ); ?></p>
	<p>size ' 30%' :<br/><?php $this->copy_text( 'copy_text_4', [], 30 ); ?></p>
	<p>size ' 50%' :<br/><?php $this->copy_text( 'copy_text_5', [], 50 ); ?></p>
	<p>size '100%' :<br/><?php $this->copy_text( 'copy_text_6', [], 100 ); ?></p>

	<?php // ===== コピーテキストエリア ===== ?>

	<h3><i class="fa-regular fa-keyboard fa-fw"></i>コピーテキストエリア</h3>

	<?php $attr = array( 'placeholder' => 'コピーテキスト', 'rows' => '4' ); ?>

	<p><?php $this->copy_textarea( 'copy_textarea_1', $attr ); ?></p>
	<p><?php $this->copy_textarea( 'copy_textarea_2', [ 'required' => 'required' ], 100 ); ?></p>

	<?php // ===== 送信ボタン ===== ?>

	<p><?php $this->submit( '保存' ); ?></p>

<?php // ======================================================================
