/**
 * Tabs functionality.
 */
document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById( 'product-visibility__meta' );

	initialize();

	/**
	 * Initialize functionality.
	 */
	function initialize() {
		if ( ! container ) {
			return;
		}

		const tabs = container.querySelectorAll( '.category-tabs .tab' );

		for ( const tab of tabs ) {
			tab.addEventListener( 'click', tabClick );
		}
	}

	/**
	 * On tab click action.
	 *
	 * @param {Event} event Current event
	 */
	function tabClick( event ) {
		event.preventDefault();

		const activeTab = container.querySelector(
			'.category-tabs > .tabs > a'
		);
		const activeTabPanel = document.getElementById(
			activeTab.href.split( '#' ).pop()
		);
		const currentTarget = event.currentTarget;
		const currentTargetTabPanel = document.getElementById(
			currentTarget.href.split( '#' ).pop()
		);

		activeTab.parentNode.classList.remove( 'tabs' );
		activeTabPanel.style.display = 'none';
		currentTarget.parentNode.classList.add( 'tabs' );
		currentTargetTabPanel.style.removeProperty( 'display' );
	}
} );
