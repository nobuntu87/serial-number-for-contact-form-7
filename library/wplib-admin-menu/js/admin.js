/**
 * コピーボタン
 */
document.addEventListener( 'DOMContentLoaded', function() {
	document.querySelectorAll( '.nt-copy-button' ).forEach( function( copyButton ) {

		const copyTarget = copyButton.parentNode.querySelector( '.nt-copy-target' );

		// [イベント] ボタン押下
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

	} );

} );
