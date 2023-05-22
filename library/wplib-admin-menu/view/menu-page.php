<?php
if ( !defined( 'ABSPATH' ) ) exit;

// HTML表示 ================================================================ ?>

<?php // ===== ヘッダー表示エリア ===== ?>
<div class="header">

	<?php $this->view_header_title(); ?>
	<?php $this->view_description(); ?>

</div>

<?php // ===== コンテンツ表示エリア ===== ?>
<div class="content">

	<?php // タブ表示 ?>
	<div class="menu-tab">
		<?php $this->view_menu_tab(); ?>
	</div>

	<?php // フォーム表示 ?>
	<div class="menu-form">
		<?php $this->view_form_title(); ?>
		<?php $this->view_menu_form(); ?>
	</div>

</div>

<?php // ======================================================================
