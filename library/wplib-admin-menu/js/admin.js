/**
 * コピーボタン
 */
function addTextCopyButton( copyButton, copyTarget ) {

	// [イベント] マウスクリック
	copyButton.addEventListener( 'click', () => {
		copyTarget.focus();
	} );

	// [イベント] フォーカス
	copyTarget.addEventListener( 'focus', () => {
		// クリップボード
		copyTarget.select();
		navigator.clipboard.writeText( copyTarget.value );
		// アイコン切替
		copyButton.children[0].classList.add( 'hidden' );
		copyButton.children[1].classList.remove( 'hidden' );
	} );

	// [イベント] フォーカス解除
	copyTarget.addEventListener( 'blur', () => {
		// アイコン切替
		copyButton.children[0].classList.remove( 'hidden' );
		copyButton.children[1].classList.add( 'hidden' );
	} );

}
document.addEventListener( 'DOMContentLoaded', function() {
	document.querySelectorAll(
		'.nt-wplib-admin-menu-wrap .nt-copy-button'
	).forEach( function( copyButton ) {
		addTextCopyButton( copyButton,
			copyButton.parentNode.querySelector(
				'.nt-copy-target'
			)
		);
	} );
} );
