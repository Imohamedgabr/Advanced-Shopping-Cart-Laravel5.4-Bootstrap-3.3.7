<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2017
 */

$enc = $this->encoder();


?>
<?php if( isset( $this->itemsStockUrl ) ) : ?>
	<script type="text/javascript" defer="defer" src="<?= $enc->attr( $this->itemsStockUrl ); ?>"></script>
<?php endif; ?>
