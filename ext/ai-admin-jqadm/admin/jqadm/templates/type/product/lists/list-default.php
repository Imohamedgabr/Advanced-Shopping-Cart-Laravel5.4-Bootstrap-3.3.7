<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */

$enc = $this->encoder();


$target = $this->config( 'admin/jqadm/url/search/target' );
$controller = $this->config( 'admin/jqadm/url/search/controller', 'Jqadm' );
$action = $this->config( 'admin/jqadm/url/search/action', 'search' );
$config = $this->config( 'admin/jqadm/url/search/config', [] );


$newTarget = $this->config( 'admin/jqadm/url/create/target' );
$newCntl = $this->config( 'admin/jqadm/url/create/controller', 'Jqadm' );
$newAction = $this->config( 'admin/jqadm/url/create/action', 'create' );
$newConfig = $this->config( 'admin/jqadm/url/create/config', [] );


$getTarget = $this->config( 'admin/jqadm/url/get/target' );
$getCntl = $this->config( 'admin/jqadm/url/get/controller', 'Jqadm' );
$getAction = $this->config( 'admin/jqadm/url/get/action', 'get' );
$getConfig = $this->config( 'admin/jqadm/url/get/config', [] );


$copyTarget = $this->config( 'admin/jqadm/url/copy/target' );
$copyCntl = $this->config( 'admin/jqadm/url/copy/controller', 'Jqadm' );
$copyAction = $this->config( 'admin/jqadm/url/copy/action', 'copy' );
$copyConfig = $this->config( 'admin/jqadm/url/copy/config', [] );


$delTarget = $this->config( 'admin/jqadm/url/delete/target' );
$delCntl = $this->config( 'admin/jqadm/url/delete/controller', 'Jqadm' );
$delAction = $this->config( 'admin/jqadm/url/delete/action', 'delete' );
$delConfig = $this->config( 'admin/jqadm/url/delete/config', [] );


/** admin/jqadm/type/product/lists/fields
 * List of product list type columns that should be displayed in the list view
 *
 * Changes the list of product list type columns shown by default in the product
 * list type list view. The columns can be changed by the editor as required within the
 * administraiton interface.
 *
 * The names of the colums are in fact the search keys defined by the managers,
 * e.g. "product.lists.type.id" for the product type ID.
 *
 * @param array List of field names, i.e. search keys
 * @since 2017.10
 * @category Developer
 */
$default = ['product.lists.type.domain', 'product.lists.type.status', 'product.lists.type.code', 'product.lists.type.label'];
$default = $this->config( 'admin/jqadm/type/product/lists/fields', $default );
$fields = $this->session( 'aimeos/admin/jqadm/type/product/lists/fields', $default );

$params = $this->get( 'pageParams', [] );
$sortcode = $this->param( 'sort' );

$typeList = [];
foreach( $this->get( 'itemTypes', [] ) as $id => $typeItem ) {
	$typeList[$id] = $typeItem->getCode();
}

$columnList = [
	'product.lists.type.id' => $this->translate( 'admin', 'ID' ),
	'product.lists.type.domain' => $this->translate( 'admin', 'Domain' ),
	'product.lists.type.status' => $this->translate( 'admin', 'Status' ),
	'product.lists.type.code' => $this->translate( 'admin', 'Code' ),
	'product.lists.type.label' => $this->translate( 'admin', 'Label' ),
	'product.lists.type.ctime' => $this->translate( 'admin', 'Created' ),
	'product.lists.type.mtime' => $this->translate( 'admin', 'Modified' ),
	'product.lists.type.editor' => $this->translate( 'admin', 'Editor' ),
];

?>
<?php $this->block()->start( 'jqadm_content' ); ?>

<nav class="main-navbar">

	<span class="navbar-brand">
		<?= $enc->html( $this->translate( 'admin', 'Product Lists Types' ) ); ?>
		<span class="navbar-secondary">(<?= $enc->html( $this->site()->label() ); ?>)</span>
	</span>

	<?= $this->partial(
		$this->config( 'admin/jqadm/partial/navsearch', 'common/partials/navsearch-default.php' ), [
			'filter' => $this->session( 'aimeos/admin/jqadm/type/product/lists/filter', [] ),
			'filterAttributes' => $this->get( 'filterAttributes', [] ),
			'filterOperators' => $this->get( 'filterOperators', [] ),
			'params' => $params,
		]
	); ?>
</nav>


<?= $this->partial(
		$this->config( 'admin/jqadm/partial/pagination', 'common/partials/pagination-default.php' ),
		['pageParams' => $params, 'pos' => 'top', 'total' => $this->get( 'total' ),
		'page' => $this->session( 'aimeos/admin/jqadm/type/product/lists/page', [] )]
	);
?>

<form class="list list-product-lists-type" method="POST" action="<?= $enc->attr( $this->url( $target, $controller, $action, $params, [], $config ) ); ?>">
	<?= $this->csrf()->formfield(); ?>

	<table class="list-items table table-hover table-striped">
		<thead class="list-header">
			<tr>
				<?= $this->partial(
						$this->config( 'admin/jqadm/partial/listhead', 'common/partials/listhead-default.php' ),
						['fields' => $fields, 'params' => $params, 'data' => $columnList, 'sort' => $this->session( 'aimeos/admin/jqadm/type/product/lists/sort' )]
					);
				?>

				<th class="actions">
					<a class="btn fa act-add" tabindex="1"
						href="<?= $enc->attr( $this->url( $newTarget, $newCntl, $newAction, $params, [], $newConfig ) ); ?>"
						title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)') ); ?>"
						aria-label="<?= $enc->attr( $this->translate( 'admin', 'Add' ) ); ?>">
					</a>

					<?= $this->partial(
							$this->config( 'admin/jqadm/partial/columns', 'common/partials/columns-default.php' ),
							['fields' => $fields, 'data' => $columnList]
						);
					?>
				</th>
			</tr>
		</thead>
		<tbody>

			<?= $this->partial(
				$this->config( 'admin/jqadm/partial/listsearch', 'common/partials/listsearch-default.php' ), [
					'fields' => $fields, 'filter' => $this->session( 'aimeos/admin/jqadm/type/product/lists/filter', [] ),
					'data' => [
						'product.lists.type.id' => ['op' => '=='],
						'product.lists.type.domain' => ['op' => '==', 'type' => 'select', 'val' => [
							'attribute' => $this->translate( 'admin', 'attribute' ),
							'catalog' => $this->translate( 'admin', 'catalog' ),
							'customer' => $this->translate( 'admin', 'customer' ),
							'media' => $this->translate( 'admin', 'media' ),
							'price' => $this->translate( 'admin', 'price' ),
							'product' => $this->translate( 'admin', 'product' ),
							'service' => $this->translate( 'admin', 'service' ),
							'supplier' => $this->translate( 'admin', 'supplier' ),
							'text' => $this->translate( 'admin', 'text' ),
						]],
						'product.lists.type.status' => ['op' => '==', 'type' => 'select', 'val' => [
							'1' => $this->translate( 'admin', 'status:enabled' ),
							'0' => $this->translate( 'admin', 'status:disabled' ),
							'-1' => $this->translate( 'admin', 'status:review' ),
							'-2' => $this->translate( 'admin', 'status:archive' ),
						]],
						'product.lists.type.code' => [],
						'product.lists.type.label' => [],
						'product.lists.type.ctime' => ['op' => '>=', 'type' => 'datetime-local'],
						'product.lists.type.mtime' => ['op' => '>=', 'type' => 'datetime-local'],
						'product.lists.type.editor' => [],
					]
				] );
			?>

			<?php foreach( $this->get( 'items', [] ) as $id => $item ) : ?>
				<?php $url = $enc->attr( $this->url( $getTarget, $getCntl, $getAction, ['id' => $id] + $params, [], $getConfig ) ); ?>
				<tr class="<?= $this->site()->readonly( $item->getSiteId() ); ?>">
					<?php if( in_array( 'product.lists.type.id', $fields ) ) : ?>
						<td class="product-type-id"><a class="items-field" href="<?= $url; ?>"><?= $enc->html( $item->getId() ); ?></a></td>
					<?php endif; ?>
					<?php if( in_array( 'product.lists.type.domain', $fields ) ) : ?>
						<td class="product-type-domain"><a class="items-field" href="<?= $url; ?>"><?= $enc->html( $item->getDomain() ); ?></a></td>
					<?php endif; ?>
					<?php if( in_array( 'product.lists.type.status', $fields ) ) : ?>
						<td class="product-type-status"><a class="items-field" href="<?= $url; ?>"><div class="fa status-<?= $enc->attr( $item->getStatus() ); ?>"></div></a></td>
					<?php endif; ?>
					<?php if( in_array( 'product.lists.type.code', $fields ) ) : ?>
						<td class="product-type-code"><a class="items-field" href="<?= $url; ?>" tabindex="1"><?= $enc->html( $item->getCode() ); ?></a></td>
					<?php endif; ?>
					<?php if( in_array( 'product.lists.type.label', $fields ) ) : ?>
						<td class="product-type-label"><a class="items-field" href="<?= $url; ?>"><?= $enc->html( $item->getLabel() ); ?></a></td>
					<?php endif; ?>
					<?php if( in_array( 'product.lists.type.ctime', $fields ) ) : ?>
						<td class="product-type-ctime"><a class="items-field" href="<?= $url; ?>"><?= $enc->html( $item->getTimeCreated() ); ?></a></td>
					<?php endif; ?>
					<?php if( in_array( 'product.lists.type.mtime', $fields ) ) : ?>
						<td class="product-type-mtime"><a class="items-field" href="<?= $url; ?>"><?= $enc->html( $item->getTimeModified() ); ?></a></td>
					<?php endif; ?>
					<?php if( in_array( 'product.lists.type.editor', $fields ) ) : ?>
						<td class="product-type-editor"><a class="items-field" href="<?= $url; ?>"><?= $enc->html( $item->getEditor() ); ?></a></td>
					<?php endif; ?>

					<td class="actions">
						<?php if( !$this->site()->readonly( $item->getSiteId() ) ) : ?>
							<a class="btn act-delete fa" tabindex="1"
								href="<?= $enc->attr( $this->url( $delTarget, $delCntl, $delAction, ['id' => $id] + $params, [], $delConfig ) ); ?>"
								title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry') ); ?>"
								aria-label="<?= $enc->attr( $this->translate( 'admin', 'Delete' ) ); ?>"></a>
						<?php endif; ?>
						<a class="btn act-copy fa" tabindex="1"
							href="<?= $enc->attr( $this->url( $copyTarget, $copyCntl, $copyAction, ['id' => $id] + $params, [], $copyConfig ) ); ?>"
							title="<?= $enc->attr( $this->translate( 'admin', 'Copy this entry') ); ?>"
							aria-label="<?= $enc->attr( $this->translate( 'admin', 'Copy' ) ); ?>"></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if( $this->get( 'items', [] ) === [] ) : ?>
		<div class="noitems"><?= $enc->html( sprintf( $this->translate( 'admin', 'No items found' ) ) ); ?></div>
	<?php endif; ?>
</form>

<?= $this->partial(
		$this->config( 'admin/jqadm/partial/pagination', 'common/partials/pagination-default.php' ),
		['pageParams' => $params, 'pos' => 'bottom', 'total' => $this->get( 'total' ),
		'page' => $this->session( 'aimeos/admin/jqadm/type/product/lists/page', [] )]
	);
?>

<?php $this->block()->stop(); ?>

<?= $this->render( $this->config( 'admin/jqadm/template/page', 'common/page-default.php' ) ); ?>
